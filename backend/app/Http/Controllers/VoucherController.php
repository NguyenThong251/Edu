<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class VoucherController extends Controller
{
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

        $voucher = Voucher::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Voucher created successfully',
            'data' => $voucher
        ], 201);
    }

    // Kiểm tra voucher có hợp lệ không
    public function validateVoucher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|exists:vouchers,code',
            'order_total' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $voucher = Voucher::where('code', $request->code)->first();

        if (!$voucher->isValid($request->order_total)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Voucher is invalid, expired, inactive, or does not meet order minimum',
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Voucher is valid',
            'data' => $voucher,
        ], 200);
    }

    // Áp dụng voucher cho đơn hàng
    public function applyVoucher(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|exists:vouchers,code',
            'order_total' => 'required|numeric|min:0',
        ]);

        $voucher = Voucher::where('code', $data['code'])->first();

        if (!$voucher->isValid($data['order_total'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Voucher is invalid, expired, inactive, or does not meet order minimum',
            ], 400);
        }

        $discountedTotal = $data['order_total'] - $voucher->applyDiscount($data['order_total']);

        // Tăng số lần sử dụng voucher
        $voucher->increment('usage_count');

        return response()->json([
            'status' => 'success',
            'message' => 'Voucher applied successfully',
            'discounted_total' => $discountedTotal,
            'voucher' => $voucher,
        ], 200);
    }

    // Hủy voucher đã áp dụng
    public function cancelVoucher(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|exists:vouchers,code',
        ]);

        $voucher = Voucher::where('code', $data['code'])->first();

        if ($voucher->usage_count > 0) {
            $voucher->decrement('usage_count');
            return response()->json([
                'status' => 'success',
                'message' => 'Voucher canceled successfully',
                'voucher' => $voucher,
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Voucher usage count is already zero',
        ], 400);
    }
}
