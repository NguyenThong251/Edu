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

        // Lấy giỏ hàng của người dùng
        $cart = Cart::where('user_id', $user->id)->first();
        if (!$cart) {
            return response()->json(['message' => 'Giỏ hàng trống'], 400);
        }

        // Tính tổng giá của giỏ hàng
        $cartItems = CartItem::where('cart_id', $cart->id)->get();
        $totalPrice = $cartItems->sum(function ($item) {
            return $item->price;
        });

        // Tạo mã đơn hàng
        $orderCode = Str::uuid()->toString();

        // Bắt đầu transaction
        DB::beginTransaction();

        try {
            // Tạo đơn hàng mới
            $order = Order::create([
                'user_id' => $user->id,
                'order_code' => $orderCode,
                'total_price' => $totalPrice,
                'payment_method' => 'unpaid', // hoặc 'stripe', 'paypal', tùy theo thiết lập cổng thanh toán
                'payment_status' => 'pending',
            ]);

            // Tạo các mục trong đơn hàng từ giỏ hàng
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'course_id' => $item->course_id,
                    'price' => $item->price,
                ]);
            }

            // Xóa các mục trong giỏ hàng sau khi tạo đơn hàng
            CartItem::where('cart_id', $cart->id)->delete();

            // Hoàn tất transaction
            DB::commit();

            return response()->json(['order' => $order, 'message' => 'Đơn hàng đã được tạo thành công'], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Lỗi khi tạo đơn hàng: ' . $e->getMessage()], 500);
        }
    }
}
