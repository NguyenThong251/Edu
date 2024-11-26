<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PayoutRequest;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\Review;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminController extends Controller
{
    public function getAdminReport(Request $request)
    {
        try {
            // Doanh thu tổng từ tất cả các đơn hàng đã thanh toán
            $totalRevenue = Order::where('payment_status', 'paid')->sum('total_price');

            // Tổng số tiền đã được rút (payouts)
            $totalPayouts = PayoutRequest::where('status', 'completed')->sum('amount');

            // Doanh thu sau khi đã thanh toán (Net Revenue)
            $netRevenue = $totalRevenue - $totalPayouts;

            $totalUsers = User::count();

            $totalCourses = Course::count();

            $totalCategories = Category::count();

            $totalPayoutRequests = PayoutRequest::count();

            $completedPayoutRequests = PayoutRequest::where('status', 'completed')->count();

            $processingPayoutRequests = PayoutRequest::where('status', 'processing')->count();

            $failedPayoutRequests = PayoutRequest::where('status', 'failed')->count();

            return response()->json([
                'status' => 'OK',
                'data' => [
                    'total_revenue' => $totalRevenue,
                    'total_payouts' => $totalPayouts,
                    'net_revenue' => $netRevenue,
                    'total_users' => $totalUsers,
                    'total_courses' => $totalCourses,
                    'total_categories' => $totalCategories,
                    'total_payout_requests' => $totalPayoutRequests,
                    'completed_payout_requests' => $completedPayoutRequests,
                    'processing_payout_requests' => $processingPayoutRequests,
                    'failed_payout_requests' => $failedPayoutRequests,
                ],
                'message' => 'Get admin report successfully.',
                'code' => 200
            ], 200);
        } catch (\Exception $e) {
            // Xử lý lỗi
            return response()->json([
                'status' => 'FAIL',
                'message' => $e->getMessage(),
                'code' => 500
            ], 500);
        }
    }


    public function getAdminLineChartData(Request $request)
    {
        try {
            // Lấy các tham số từ request
            $startDate = $request->input('start_date'); // Ngày bắt đầu
            $endDate = $request->input('end_date');     // Ngày kết thúc
            $filter = $request->input('filter', 'day'); // Lọc theo ngày, tháng, năm

            // Xử lý các tham số
            $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->subMonth()->startOfDay();
            $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now()->endOfDay();

            // Xác định khoảng thời gian dựa trên filter
            if ($filter === 'month') {
                $period = $startDate->monthsUntil($endDate);
            } elseif ($filter === 'year') {
                $period = $startDate->yearsUntil($endDate);
            } else { // default là 'day'
                $period = $startDate->daysUntil($endDate);
            }

            // Lấy dữ liệu doanh thu từ bảng orders
            $revenueData = Order::select(
                DB::raw($filter === 'month' ? "DATE_FORMAT(created_at, '%Y-%m')" : ($filter === 'year' ? "DATE_FORMAT(created_at, '%Y')" : "DATE(created_at)")),
                DB::raw("SUM(total_price) as revenue"),
                DB::raw("COUNT(id) as total_sales")
            )
                ->where('payment_status', 'paid')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy(DB::raw($filter === 'month' ? "DATE_FORMAT(created_at, '%Y-%m')" : ($filter === 'year' ? "DATE_FORMAT(created_at, '%Y')" : "DATE(created_at)")))
                ->orderBy('created_at', 'asc')
                ->get();

            // Chuyển dữ liệu thành dạng mảng [ngày/tháng/năm => doanh thu]
            $data = $revenueData->keyBy(function ($item) use ($filter) {
                return $filter === 'month' ? $item->created_at : ($filter === 'year' ? $item->created_at : $item->created_at);
            })->map(function ($item) {
                return [
                    'revenue' => (float)$item->revenue,
                    'total_sales' => (int)$item->total_sales,
                ];
            });

            // Chuẩn bị dữ liệu kết quả với các khoảng thời gian không có doanh thu hiển thị 0
            $result = [];
            foreach ($period as $date) {
                if ($filter === 'month') {
                    $formattedDate = $date->format('Y-m');
                } elseif ($filter === 'year') {
                    $formattedDate = $date->format('Y');
                } else { // 'day'
                    $formattedDate = $date->format('Y-m-d');
                }

                $result[] = [
                    'date' => $formattedDate,
                    'revenue' => isset($data[$formattedDate]) ? $data[$formattedDate]['revenue'] : 0,
                    'total_sales' => isset($data[$formattedDate]) ? $data[$formattedDate]['total_sales'] : 0,
                ];
            }

            // Tính tổng revenue và tổng sale
            $totalRevenue = $revenueData->sum('revenue');
            $totalSales = $revenueData->sum('total_sales');

            return response()->json([
                'status' => 'OK',
                'data' => $result,
                'total_revenue' => $totalRevenue,
                'total_sales' => $totalSales,
                'code' => 200
            ], 200);
        } catch (\Exception $e) {
            // Xử lý lỗi
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage(),
                'code' => 500
            ], 500);
        }
    }

    public function getUserRegistrationLineChart(Request $request)
    {
        try {
            // Lấy các tham số từ request
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $filter = $request->input('filter', 'day'); // 'day', 'month', 'year'

            // Xử lý các tham số và thiết lập khoảng thời gian
            $startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->subMonth()->startOfDay();
            $endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

            // Xác định định dạng và tạo khoảng thời gian dựa trên filter
            switch ($filter) {
                case 'month':
                    $format = 'Y-m';
                    $period = CarbonPeriod::create($startDate, '1 month', $endDate);
                    break;
                case 'year':
                    $format = 'Y';
                    $period = CarbonPeriod::create($startDate, '1 year', $endDate);
                    break;
                case 'day':
                default:
                    $format = 'Y-m-d';
                    $period = CarbonPeriod::create($startDate, '1 day', $endDate);
                    break;
            }

            // Lấy dữ liệu đăng ký người dùng
            $registrationData = User::select(
                DB::raw("DATE_FORMAT(created_at, '{$format}') as period"),
                DB::raw("COUNT(id) as registrations")
            )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('period')
                ->orderBy('period', 'asc')
                ->get();

            // Chuyển dữ liệu thành dạng mảng [period => registrations]
            $data = $registrationData->pluck('registrations', 'period')->toArray();

            // Chuẩn bị dữ liệu kết quả với các khoảng thời gian không có đăng ký hiển thị 0
            $result = [];
            foreach ($period as $date) {
                $formattedDate = $date->format($format);
                $result[] = [
                    'period' => $formattedDate,
                    'registrations' => isset($data[$formattedDate]) ? (int) $data[$formattedDate] : 0,
                ];
            }

            // Tính tổng đăng ký
            $totalRegistrations = array_sum($data);

            return response()->json([
                'status' => 'OK',
                'data' => $result,
                'total_registrations' => $totalRegistrations,
                'code' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage(),
                'code' => 500
            ], 500);
        }
    }


    public function getOrderLineChartData(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $filter = $request->input('filter', 'day');

            $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->subMonth()->startOfDay();
            $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now()->endOfDay();

            if ($filter === 'month') {
                $period = $startDate->monthsUntil($endDate);
                $format = 'Y-m';
            } elseif ($filter === 'year') {
                $period = $startDate->yearsUntil($endDate);
                $format = 'Y';
            } else { // 'day'
                $period = $startDate->daysUntil($endDate);
                $format = 'Y-m-d';
            }

            // Lấy dữ liệu đơn hàng
            $orderData = Order::select(
                DB::raw("DATE_FORMAT(created_at, '{$format}') as period"),
                DB::raw("COUNT(id) as orders")
            )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('period')
                ->orderBy('period', 'asc')
                ->get();

            // Chuyển dữ liệu thành dạng mảng [period => orders]
            $data = $orderData->keyBy('period')->map(function ($item) {
                return [
                    'orders' => $item->orders,
                ];
            });

            // Chuẩn bị dữ liệu kết quả với các khoảng thời gian không có đơn hàng hiển thị 0
            $result = [];
            foreach ($period as $date) {
                $formattedDate = $date->format($format);
                $result[] = [
                    'period' => $formattedDate,
                    'orders' => $data->get($formattedDate)['orders'] ?? 0,
                ];
            }

            // Tính tổng đơn hàng
            $totalOrders = $orderData->sum('orders');

            return response()->json([
                'status' => 'OK',
                'data' => $result,
                'total_orders' => $totalOrders,
                'code' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage(),
                'code' => 500
            ], 500);
        }
    }

}
