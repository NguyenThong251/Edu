<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PayoutRequest;
use App\Models\Wishlist;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Tymon\JWTAuth\Facades\JWTAuth;

class InstructorController extends Controller
{
    // get list of instructors list of their courses.
    //     public function getListCourses(Request $request)
    //     {
    //         $user = auth()->user();
    //         $title = $request->input('title', null);
    //         $status = $request->input('status', null);
    //         $page = $request->input('page', 1);
    //         $perPage = $request->input('per_page', 10);
    //         $priceOrder = $request->input('price_order', null);


    //         $query = Course::where('created_by', $user->id);
    //         if ($status) {
    //             $query->where('status', $status);
    //         }
    //         if ($title) {
    //             $query->where('title', 'like', '%' . $title . '%');
    //         }
    //         if ($priceOrder && in_array(strtolower($priceOrder), ['asc', 'desc'])) {
    //             $query->orderBy('price', strtolower($priceOrder));
    //         } else {
    //             $query->orderBy('created_at', 'desc');
    //         }
    //         $courses = $query->paginate($perPage, ['*'], 'page', $page);

    // //        return formatResponse(STATUS_OK,$courses->items(),'','Get list success',CODE_OK);
    //         return response()->json(['status' => 'OK', 'data' => $courses->items(),
    //             'pagination' => [
    //                 'total' => $courses->total(),
    //                 'current_page' => $courses->currentPage(),
    //                 'last_page' => $courses->lastPage(),
    //                 'per_page' => $courses->perPage(),
    //             ],
    //             'code' => 200
    //         ]);
    //     }
    public function getListCourses(Request $request)
    {
        $user = auth()->user();
        $title = $request->input('title', null);
        $status = $request->input('status', null);
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $priceOrder = $request->input('price_order', null);

        $query = Course::where('created_by', $user->id);

        if ($status) {
            $query->where('status', $status);
        }
        if ($title) {
            $query->where('title', 'like', '%' . $title . '%');
        }
        if ($priceOrder && in_array(strtolower($priceOrder), ['asc', 'desc'])) {
            $query->orderBy('price', strtolower($priceOrder));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Gộp quan hệ
        $query->with([
            'category',
            'level',
            'language',
            'creator:id,first_name,last_name',
            'sections.lectures',
            'reviews'
        ])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        $courses = $query->paginate($perPage, ['*'], 'page', $page);

        // Định dạng lại dữ liệu
        $formattedCourses = $courses->map(function ($course) {
            $lectures_count = $course->sections->sum(fn($section) => $section->lectures->count());
            $total_duration = $course->sections->sum(fn($section) => $section->lectures->sum('duration'));
            // return [
            //     'id' => $course->id,
            //     'title' => $course->title,
            //     'old_price' => round($course->price, 0),
            //     'current_price' => round(
            //         $course->type_sale === 'price' ? $course->price - $course->sale_value : $course->price * (1 - $course->sale_value / 100),
            //         0
            //     ),
            //     'thumbnail' => $course->thumbnail,
            //     'level' => $course->level->name ?? null,
            //     'language' => $course->language->name ?? null,
            //     'creator' => ($course->creator && ($course->creator->last_name || $course->creator->first_name)
            //         ? trim($course->creator->last_name . ' ' . $course->creator->first_name)
            //         : ''),
            //     'lectures_count' => $lectures_count,
            //     'total_duration' => round($total_duration / 60 / 60, 1), // Đổi từ giây sang giờ
            //     'rating_avg' => round($course->reviews_avg_rating, 2) ?? 0,
            //     'reviews_count' => $course->reviews_count ?? 0,
            //     'status' => $course->status,
            // ];
            return [
                'id' => $course->id,
                'category_id' => $course->category_id,
                'level_id' => $course->level_id,
                'language_id' => $course->language_id,
                'title' => $course->title, 
                'description' => $course->description,
                'short_description' => $course->short_description,
                'price' => round($course->price, 0),
                'type_sale' => $course->type_sale ?? '',
                'sale_value' => $course->sale_value ?? '',
                'current_price' => round(
                    $course->type_sale === 'price'
                        ? $course->price - $course->sale_value
                        : $course->price * (1 - $course->sale_value / 100),
                    0
                ),
                'thumbnail' => $course->thumbnail,
                'level' => $course->level->name ?? null,
                'language' => $course->language->name ?? null,
                'creator' => $course->creator
                    ? trim($course->creator->first_name . ' ' . $course->creator->last_name)
                    : '',
                'lectures_count' => $lectures_count,
                'total_duration' => round($total_duration / 60 / 60, 1), // Đổi từ giây sang giờ
                'rating_avg' => round($course->reviews_avg_rating, 2) ?? 0,
                'reviews_count' => $course->reviews_count ?? 0,
                'status' => $course->status,
            ];
        });

        // Trả về dữ liệu
        return response()->json([
            'status' => 'OK',
            'data' => $formattedCourses,
            'pagination' => [
                'total' => $courses->total(),
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
            ],
            'code' => 200
        ]);
    }

    public function getReport(Request $request)
    {
        try {
            $user = auth()->user();

            // Tính tổng số khóa học của giáo viên
            $totalCourses = Course::where('created_by', $user->id)->count();

            // Tính tổng số danh mục mà giáo viên đã tạo ra các khóa học
            $totalCategories = Course::where('created_by', $user->id)
                ->distinct('category_id')
                ->count('category_id');

            // Tính tổng doanh thu các khóa học của giáo viên dựa vào bảng orders và order_items
            $totalRevenue = OrderItem::whereIn('course_id', function ($query) use ($user) {
                $query->select('id')
                    ->from('courses')
                    ->where('created_by', $user->id);
            })
                ->whereHas('order', function ($query) {
                    $query->where('payment_status', 'paid');
                })
                ->sum('price');

            // Tính tổng số học viên đã mua khóa học của giáo viên
            $totalStudents = Order::whereHas('orderItems.course', function ($query) use ($user) {
                $query->where('created_by', $user->id);
            })
                ->where('payment_status', 'paid')
                ->distinct('user_id')
                ->count('user_id');

            // Hoặc nếu bạn muốn tính tổng số đơn hàng (có thể học viên mua nhiều khóa cùng lúc)
            // $totalStudents = Order::whereHas('orderItems.course', function($query) use ($user) {
            //         $query->where('created_by', $user->id);
            //     })
            //     ->where('payment_status', 'paid')
            //     ->count();

            // Trả về dữ liệu báo cáo
            return response()->json([
                'status' => 'OK',
                'data' => [
                    'total_courses' => $totalCourses,
                    'total_categories' => $totalCategories,
                    'total_revenue' => $totalRevenue,
                    'total_students' => $totalStudents,
                ],
                'error' => '',
                'message' => 'Get instructor report success',
                'code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => $e->getMessage(),
                'code' => 500
            ], 500);
        }
    }


    public function getLineChartData(Request $request)
    {
        $user = auth()->user();

        // Lấy các tham số từ request
        $startDate = $request->input('start_date'); // Ngày bắt đầu
        $endDate = $request->input('end_date');     // Ngày kết thúc
        $filter = $request->input('filter', 'day'); // Lọc theo ngày, tháng, năm

        // Xử lý các tham số
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now()->endOfMonth();

        $period = $filter === 'month'
            ? $startDate->monthsUntil($endDate)
            : $startDate->daysUntil($endDate);

        // Lấy dữ liệu doanh thu
        $revenueData = OrderItem::select(
            DB::raw("DATE(created_at) as date"),
            DB::raw("SUM(price) as revenue"),
            DB::raw("COUNT(id) as total_sales")
        )
            ->whereHas('course', function ($query) use ($user) {
                $query->where('created_by', $user->id); // Chỉ lấy dữ liệu của giáo viên hiện tại
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw("DATE(created_at)"))
            ->get();

        // Chuyển dữ liệu thành dạng mảng [ngày => doanh thu]
        $data = $revenueData->keyBy('date')->map(function ($item) {
            return [
                'revenue' => $item->revenue,
                'total_sales' => $item->total_sales,
            ];
        });

        // Chuẩn bị dữ liệu kết quả với các ngày không có doanh thu hiển thị 0
        $result = [];
        foreach ($period as $date) {
            $formattedDate = $filter === 'month' ? $date->format('Y-m') : $date->format('Y-m-d');
            $result[] = [
                'date' => $formattedDate,
                'revenue' => $data->get($formattedDate)['revenue'] ?? 0,
                'total_sales' => $data->get($formattedDate)['total_sales'] ?? 0,
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
        ]);
    }

    public function getCourseStatistics(Request $request)
    {
        $user = auth()->user();

        $activeCourses = Course::where('created_by', $user->id)
            ->where('status', 'active')
            ->count();
        $inActiveCourses = Course::where('created_by', $user->id)
            ->where('status', 'inactive')
            ->count();

        $freeCourses = Course::where('created_by', $user->id)
            ->where('price', 0)
            ->where('status', 'active')
            ->count();

        $paidCourses = Course::where('created_by', $user->id)
            ->where('price', '>', 0)
            ->where('status', 'active')
            ->count();

        $totalCourses = Course::where('created_by', $user->id)
            ->count();

        return response()->json([
            'status' => 'OK',
            'data' => [
                'total_courses' => $totalCourses,
                'active_courses' => $activeCourses,
                'inactive_courses' => $inActiveCourses,
                'free_courses' => $freeCourses,
                'paid_courses' => $paidCourses,
            ],
            'code' => 200
        ]);
    }
}
