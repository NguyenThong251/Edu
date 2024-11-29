<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Pagination\LengthAwarePaginator;

class VoucherController extends Controller
{
    public function getListAdmin(Request $request)
    {
        // Query cơ bản lấy danh sách voucher
        $vouchersQuery = Voucher::query();

        // Kiểm tra tham số `deleted`
        if ($request->has('deleted') && $request->deleted == 1) {
            $vouchersQuery->onlyTrashed(); // Lấy các voucher đã xóa
        } else {
            $vouchersQuery->whereNull('deleted_at'); // Mặc định chỉ lấy voucher chưa xóa
        }

        // Lọc theo keyword (nếu có)
        if ($request->has('keyword') && !empty($request->keyword)) {
            $keyword = $request->keyword;
            $vouchersQuery->where('code', 'like', '%' . $keyword . '%');
        }

        // Lọc theo trạng thái (nếu có)
        if ($request->has('status') && !is_null($request->status)) {
            $vouchersQuery->where('status', $request->status);
        }

        // Sắp xếp theo `created_at` (mặc định là `desc`)
        $order = $request->get('order', 'desc');
        $vouchersQuery->orderBy('created_at', $order);


        $order = $request->get('order', 'desc'); // Giá trị mặc định là desc
        $vouchersQuery->orderBy('created_at', $order);

        // Phân trang với per_page và page
        $perPage = (int) $request->get('per_page', 10); // Số lượng bản ghi mỗi trang, mặc định 10
        $page = (int) $request->get('page', 1); // Trang hiện tại, mặc định 1

        // Lấy danh sách đã lọc
        $vouchers = $vouchersQuery->get();

        // Tổng số lượng bản ghi
        $total = $vouchers->count();

        // Phân trang thủ công
        $paginatedVouchers = $vouchers->forPage($page, $perPage)->values();

        // Tạo đối tượng LengthAwarePaginator
        $pagination = new LengthAwarePaginator(
            $paginatedVouchers, // Dữ liệu cho trang hiện tại
            $total,                 // Tổng số bản ghi
            $perPage,               // Số lượng bản ghi mỗi trang
            $page,                  // Trang hiện tại
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(), // Đường dẫn chính
                'query' => $request->query() // Lấy tất cả query parameters hiện tại
            ]
        );

        // Chuyển đổi dữ liệu phân trang thành mảng
        $vouchers = $pagination->toArray();

        // Trả về kết quả với đầy đủ thông tin filter, order và phân trang
        return formatResponse(
            STATUS_OK,
            $vouchers,
            '',
            __('messages.course_level_fetch_success')
        );
    }

    // Lấy toàn bộ voucher (chỉ trả về voucher `active`)
    public function index()
    {
        $vouchers = Voucher::where('status', 'active')->paginate('10');
        return response()->json(['status' => 'success', 'result' => count($vouchers), 'data' => $vouchers], 200);
    }

    // Lấy chi tiết một voucher theo ID hoặc mã code (kèm trạng thái active)
    public function show($idOrCode)
    {
        $voucher = Voucher::where(function ($query) use ($idOrCode) {
            $query->where('id', $idOrCode)->orWhere('code', $idOrCode);
        })->where('status', 'active')->firstOrFail();

        return response()->json(['status' => 'success', 'data' => $voucher], 200);
    }

    // Lấy danh sách voucher đã bị soft delete
    public function getDeletedVouchers()
    {
        $deletedVouchers = Voucher::getAllDeleted();
        return response()->json(['status' => 'success', 'data' => $deletedVouchers], 200);
    }

    // Tạo voucher mới
    public function create(Request $request)
    {
        try {
            $data = $request->validate([
                'code' => 'required|string|unique:vouchers,code',
                'description' => 'nullable|string',
                'discount_type' => 'required|in:fixed,percent',
                'discount_value' => 'required|numeric|min:0',
                'usage_limit' => 'nullable|integer|min:1',
                'expires_at' => 'nullable|date|after:today',
                'min_order_value' => 'nullable|numeric|min:0',
                'max_discount_value' => 'nullable|numeric|min:0',
                'status' => 'required|in:active,inactive',
            ]);

            $voucher = Voucher::createNewVoucher($data);
            return response()->json(['status' => 'success', 'message' => __('messages.voucher_created_success'), 'data' => $voucher], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.voucher_code_already_exists'),
            ], 400);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.voucher_creation_failed'),
            ], 500);
        }
    }

    // Xóa mềm voucher
    public function destroy(Request $request)
    {
        $data = $request->validate(['code' => 'required|string|exists:vouchers,code']);
        Voucher::softDeleteByCode($data['code']);
        return response()->json(['status' => 'success', 'message' => __('messages.voucher_soft_deleted_success')], 200);
    }

    // Khôi phục voucher đã xóa
    public function restoreVoucher(Request $request)
    {
        $data = $request->validate(['code' => 'required|string|exists:vouchers,code']);
        $voucher = Voucher::restoreByCode($data['code']);
        return response()->json(['status' => 'success', 'message' => __('messages.voucher_restore_success'), 'data' => $voucher], 200);
    }

    // Cập nhật voucher
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'code' => 'nullable|string|unique:vouchers,code,' . $id,
            'description' => 'nullable|string',
            'discount_type' => 'nullable|in:fixed,percent',
            'discount_value' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after:today',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount_value' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,inactive',
        ]);

        $voucher = Voucher::findOrFail($id);
        $voucher->updateVoucher($data);
        return response()->json(['status' => 'success', 'message' => __('messages.voucher_updated_success'), 'data' => $voucher], 200);
    }

    // Filter voucher
    public function filter(Request $request)
    {
        // dd($request->all());
        // Lấy các tham số lọc từ request
        $code = $request->input('code');
        $expires_at = $request->input('expires_at');
        $discount_type = $request->input('discount_type'); // 'percent' hoặc 'fixed'
        $min_discount_value = $request->input('min_discount_value');
        $max_discount_value = $request->input('max_discount_value');

        // Tạo query để lọc voucher
        $query = Voucher::query()->where('status', 'active');

        // Lọc theo mã voucher
        if ($code) {
            $query->where('code', 'like', '%' . $code . '%');
        }

        // Lọc theo ngày hết hạn
        if ($expires_at) {
            $query->whereDate('expires_at', '=', $expires_at);
        }

        // Lọc theo loại giá trị giảm (percent hoặc fixed)
        if ($discount_type) {
            $query->where('discount_type', $discount_type);
        }

        // Lọc theo giá trị giảm tối thiểu
        if ($min_discount_value) {
            $query->where('max_discount_value', '>=', $min_discount_value);
        }

        // Lọc theo giá trị giảm tối đa
        if ($max_discount_value) {
            $query->where('max_discount_value', '<=', $max_discount_value);
        }

        // Thực hiện phân trang kết quả
        $vouchers = $query->paginate(10);

        // Trả về kết quả
        return response()->json([
            'status' => 'success',
            'result' => $vouchers->total(),
            'data' => $vouchers
        ], 200);
    }
}
