<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        $user = auth()->user();

        DB::beginTransaction();

        try {
            // Lấy giỏ hàng của người dùng
            $cart = Cart::where('user_id', $user->id)->first();
            if (!$cart) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.cart_empty')
                ], 404);
            }

            // Tính tổng giá từ giỏ hàng
            $cartItems = CartItem::where('cart_id', $cart->id)->get();
            $totalPrice = $cartItems->sum('price');

            // Khởi tạo đơn hàng mới
            $order = Order::create([
                'user_id' => $user->id,
                'voucher_id' => $request->voucher_id, // Nếu có voucher
                'order_code' => Str::random(10),
                'total_price' => $totalPrice,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending', // Trạng thái ban đầu
                'payment_code' => Str::uuid(),
                'status' => 'active',
            ]);

            // Tạo từng OrderItem từ CartItem
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'course_id' => $cartItem->course_id,
                    'price' => $cartItem->price,
                    'status' => 'active',
                ]);
            }

            // Xóa các CartItem sau khi đã tạo đơn hàng
            $cartItems->each->delete();

            // Hoàn tất giao dịch
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('messages.order_created_success'),
                'order' => $order
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'error' => '',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
