<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Cart;


class VoucherController extends Controller
{
    // Lấy toàn bộ voucher
    public function index()
    {
        $vouchers = Voucher::all();
        return response()->json(['status' => 'success', 'result' => count($vouchers), 'data' => $vouchers], 200);
    }

    // Lấy chi tiết một voucher theo ID hoặc mã code
    public function show($idOrCode)
    {
        $voucher = Voucher::where('id', $idOrCode)->orWhere('code', $idOrCode)->firstOrFail();
        return response()->json(['status' => 'success', 'data' => $voucher], 200);
    }

    // Lấy danh sách các voucher đã bị xóa mềm
    public function getDeletedVouchers()
    {
        $deletedVouchers = Voucher::getAllDeleted();
        return response()->json(['status' => 'success', 'data' => $deletedVouchers], 200);
    }

    // Tạo voucher mới
    public function create(Request $request)
    {
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
    }

    // Soft delete voucher
    public function destroy(Request $request)
    {
        $data = $request->validate(['code' => 'required|string|exists:vouchers,code']);
        Voucher::softDeleteByCode($data['code']);
        return response()->json(['status' => 'success', 'message' => __('messages.voucher_soft_deleted_success')], 200);
    }

    // Khôi phục voucher đã bị soft deleted
    public function restoreVoucher(Request $request)
    {
        $data = $request->validate(['code' => 'required|string|exists:vouchers,code']);
        $voucher = Voucher::restoreByCode($data['code']);
        return response()->json(['status' => 'success', 'message' => __('messages.voucher_restore_success'), 'data' => $voucher], 200);
    }

    public function applyVoucher(Request $request)
    {
        $data = $request->validate(['voucher_code' => 'required|string|exists:vouchers,code']);
        $user = Auth::user();
        $cart = Cart::getOrCreateForUser($user);
        $totalPrice = $cart->cartItems->sum('current_price');

        $voucher = Voucher::where('code', $data['voucher_code'])->firstOrFail();
        $result = $voucher->apply($totalPrice, $user->id);

        return response()->json([
            'status' => 'success',
            'message' => __('messages.voucher_applied_successfully'),
            'data' => [
                'total_price' => $totalPrice,
                'discount' => $result['discount'],
                'total_price_after_discount' => $result['total_price_after_discount'],
            ]
        ]);
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
}
