<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\PaymentIntent;
use Stripe\Stripe;

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

    public function createPaymentIntent(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $order = Order::where('id', $request->order_id)->first();
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $paymentIntent = PaymentIntent::create([
            'amount' => $order->total_price * 100, // Stripe yêu cầu số tiền tính bằng cent
            'currency' => 'usd',
            'metadata' => ['order_id' => $order->id],
        ]);

        // Cập nhật mã payment_code để sử dụng trong webhook
        $order->update(['payment_code' => $paymentIntent->id]);

        return response()->json([
            'client_secret' => $paymentIntent->client_secret,
        ]);
    }

    public function listOrders(Request $request)
    {
        $user = auth()->user();

        // Lấy danh sách đơn hàng của người dùng hiện tại
        $orders = Order::where('user_id', $user->id)
            ->with(['orderItems.course'])
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Mỗi trang có 10 đơn hàng


        return response()->json([
            'status' => 'success',
            'message' => 'Orders retrieved successfully',
            'orders' => $orders
        ], 200);
    }

    public function getOrderDetails($id)
    {
        // Xác thực người dùng hiện tại
        $user = Auth::user();

        // Tìm đơn hàng dựa trên ID và người dùng hiện tại
        $order = Order::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['orderItems.course']) // Tải các mục đơn hàng và chi tiết khóa học nếu có
            ->first();

        // Kiểm tra xem đơn hàng có tồn tại không
        if (!$order) {
            return response()->json(['message' => 'Order not found or access denied'], 404);
        }

        // Trả về dữ liệu chi tiết đơn hàng
        return response()->json([
            'message' => 'Order details retrieved successfully',
            'order' => $order
        ], 200);
    }

    public function confirmPayment(Request $request, $id)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        // Tìm đơn hàng của người dùng hiện tại
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('payment_status', 'pending')
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found or already confirmed'], 404);
        }

        try {
            // Xác định đơn vị tiền tệ và số tiền
            $currency = $order->currency; // USD hoặc VND
            $amount = $currency === 'usd' ? $order->total_price * 100 : $order->total_price;

            // Tạo PaymentIntent từ Stripe
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => $currency,
                'payment_method' => $request->payment_method_id,
                'confirmation_method' => 'manual',
                'confirm' => true,
            ]);

            // Xác nhận nếu PaymentIntent thành công
            if ($paymentIntent->status == 'succeeded') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'active',
                ]);

                return response()->json([
                    'message' => 'Payment confirmed successfully',
                    'order' => $order
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Payment failed or not confirmed',
                    'payment_intent_status' => $paymentIntent->status
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error("Payment confirmation failed: " . $e->getMessage());

            return response()->json([
                'message' => 'Payment confirmation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
