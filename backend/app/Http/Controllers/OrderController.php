<?php

namespace App\Http\Controllers;

use App\Models\{Cart, CartItem, Order, OrderItem};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Log};
use Illuminate\Support\Str;
use Stripe\{PaymentIntent, PaymentMethod, Stripe, Customer};

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        // Lấy thông tin người dùng hiện tại
        $user = Auth::user();

        // Bắt đầu giao dịch
        DB::beginTransaction();

        try {
            // Lấy giỏ hàng của người dùng
            $cart = Cart::with('cartItems')->where('user_id', $user->id)->first();
            if (!$cart || $cart->cartItems->isEmpty()) {
                return response()->json(['status' => 'error', 'message' => 'Giỏ hàng trống'], 404);
            }

            // Tính tổng giá trị từ giỏ hàng
            $totalPrice = $cart->cartItems->sum('price');

            // Tạo đơn hàng mới
            $order = Order::create([
                'user_id' => $user->id,
                'voucher_id' => $request->voucher_id,
                'order_code' => Str::random(10),
                'total_price' => $totalPrice,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'payment_code' => Str::uuid(),
                'status' => 'active',
            ]);

            // Tạo các OrderItems từ CartItems
            foreach ($cart->cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'course_id' => $cartItem->course_id,
                    'price' => $cartItem->price,
                    'status' => 'active',
                ]);
            }

            // Xóa các mục trong giỏ hàng sau khi đã tạo đơn hàng
            $cart->cartItems()->delete();

            // Tạo PaymentIntent và lưu vào Order
            $paymentIntent = $this->createPaymentIntentForOrder($order);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Đơn hàng được tạo thành công',
                'order' => $order,
                'client_secret' => $paymentIntent['client_secret']
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra trong quá trình tạo đơn hàng',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    private function createPaymentIntentForOrder($order)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        // Lấy thông tin người dùng hiện tại
        $user = Auth::user();

        // Tạo `Customer` nếu chưa tồn tại và lưu vào hồ sơ người dùng
        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->name,
            ]);
            $user->update(['stripe_customer_id' => $customer->id]);
        } else {
            $customer = Customer::retrieve($user->stripe_customer_id);
        }

        // Gắn `PaymentMethod` vào `Customer` nếu cần
        $paymentMethod = 'pm_card_visa'; // Đây là ID mẫu của `PaymentMethod`, bạn có thể thay bằng ID từ request
        $retrievedPaymentMethod = PaymentMethod::retrieve($paymentMethod);
        if ($retrievedPaymentMethod->customer !== $customer->id) {
            $retrievedPaymentMethod->attach(['customer' => $customer->id]);
        }

        // Tạo `PaymentIntent` với `Customer` và `PaymentMethod` đã gắn
        $paymentIntent = PaymentIntent::create([
            'amount' => $order->total_price * 100,
            // 'currency' => $order->currency ?? 'usd',
            'currency' => 'vnd',
            'customer' => $customer->id,
            'payment_method' => $paymentMethod,
            'description' => 'Thanh toán đơn hàng #' . $order->order_code,
            'setup_future_usage' => 'off_session', // Để lưu lại PaymentMethod cho các giao dịch trong tương lai
            'automatic_payment_methods' => [
                'enabled' => true,
                'allow_redirects' => 'never'
            ]
        ]);

        // Cập nhật `payment_code` với `PaymentIntent ID` từ Stripe
        $order->update(['payment_code' => $paymentIntent->id]);

        return ['client_secret' => $paymentIntent->client_secret];
    }

    public function listOrders(Request $request)
    {
        // Lấy user hiện tại
        $userId = Auth::id();

        // Lấy danh sách đơn hàng của người dùng, kèm thông tin OrderItems và khóa học
        $orders = Order::where('user_id', $userId)
            ->with(['orderItems.course'])
            ->orderByDesc('created_at')
            ->paginate(10); // Phân trang, mỗi trang 10 đơn hàng

        // Trả về danh sách đơn hàng
        return response()->json([
            'status' => 'success',
            'message' => 'Danh sách đơn hàng được lấy thành công',
            'orders' => $orders
        ], 200);
    }

    public function getOrderDetails($id)
    {
        // Xác định người dùng hiện tại
        $userId = Auth::id();

        // Tìm đơn hàng dựa trên ID và user_id để đảm bảo quyền truy cập
        $order = Order::where('id', $id)
            ->where('user_id', $userId)
            ->with(['orderItems.course']) // Tải các mục trong đơn hàng và thông tin khóa học nếu có
            ->first();

        // Kiểm tra nếu đơn hàng không tồn tại hoặc không thuộc về người dùng hiện tại
        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đơn hàng không tồn tại hoặc bạn không có quyền truy cập'
            ], 404);
        }

        // Trả về dữ liệu chi tiết của đơn hàng
        return response()->json([
            'status' => 'success',
            'message' => 'Chi tiết đơn hàng được lấy thành công',
            'order' => $order
        ], 200);
    }

    public function confirmPayment(Request $request, $id)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        // Lấy thông tin người dùng hiện tại
        $user = Auth::user();

        // Kiểm tra nếu người dùng đã có `Customer` trên Stripe
        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->name,
            ]);
            $user->update(['stripe_customer_id' => $customer->id]);
        } else {
            $customer = Customer::retrieve($user->stripe_customer_id);
        }

        // Lấy đơn hàng của người dùng hiện tại và kiểm tra trạng thái
        $order = Order::where('id', $id)
            ->where('user_id', $user->id)
            ->where('payment_status', 'pending')
            ->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đơn hàng không tồn tại hoặc đã được thanh toán'
            ], 404);
        }

        try {
            // Lấy PaymentIntent từ payment_code của đơn hàng
            $paymentIntent = PaymentIntent::retrieve($order->payment_code);

            // Xác nhận PaymentIntent với `allow_redirects: 'never'` để tắt các phương thức yêu cầu chuyển hướng
            $paymentIntent->confirm([
                'payment_method' => $request->payment_method_id
            ]);

            // Cập nhật trạng thái đơn hàng nếu thanh toán thành công
            if ($paymentIntent->status == 'succeeded') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'active',
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Thanh toán thành công',
                    'order' => $order
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Thanh toán thất bại hoặc chưa được xác nhận',
                'payment_intent_status' => $paymentIntent->status
            ], 400);
        } catch (\Exception $e) {
            Log::error("Lỗi xác thực thanh toán: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi xác thực thanh toán',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
