<?php

namespace App\Http\Controllers;

use App\Models\{Cart, CartItem, Order, OrderItem};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Log};
use Illuminate\Support\Str;
use Stripe;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // đơn hàng mới nhất
        $orders = Order::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();
        $cartItems = CartItem::where('cart_id', $cart->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Cart is empty'], 400);
        }

        $total = round($cartItems->reduce(function ($carry, $item) {
            $price = $item->course->type_sale === 'percent'
                ? $item->course->price - ($item->course->price * $item->course->sale_value / 100)
                : $item->course->price - $item->course->sale_value;
            return $carry + $price;
        }, 0));

        $currency = $request->currency ?? 'usd';
        $total = ($currency == 'usd') ? $total * 100 : $total;

        $voucher = $request->voucher;

        try {
            DB::beginTransaction();

            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
            $response = $stripe->checkout->sessions->create([
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => $currency,
                            'product_data' => ['name' => 'Edunity Courses'],
                            'unit_amount' => $total,
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => config('services.frontend_url') . '/checkout/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => config('services.frontend_url') . '/checkout/cancel',
            ]);

            $order = Order::create([
                'user_id' => $user->id,
                'voucher_id' => $voucher ?? null,
                'order_code' => 'Edunity#' . Str::random(10),
                'total_price' => $total,
                'currency' => $currency,
                'payment_method' => 'Stripe',
                'payment_status' => 'pending',
                'payment_code' => $response->id,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'course_id' => $item->course_id,
                    'price' => $item->course->price,
                ]);
            }

            $cartItems->each->delete();
            $cart->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order created successfully',
                'checkout_url' => $response->url,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage());

            return response()->json(['status' => 'error', 'message' => 'Order creation failed'], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)
            ->where('id', $id)
            ->with('orderItems')
            ->first();

        if (!$order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $order]);
    }

    public function cancel(Request $request, $id)
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$order || $order->payment_status === 'paid') {
            return response()->json(['status' => 'error', 'message' => 'Cannot cancel this order'], 400);
        }

        $order->update(['payment_status' => 'cancelled']);

        return response()->json(['status' => 'success', 'message' => 'Order cancelled successfully']);
    }

    public function restore(Request $request, $id)
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        // Kiểm tra xem đơn hàng có thể được khôi phục hay không
        if (!$order || $order->payment_status !== 'cancelled') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only cancelled orders can be restored',
            ], 400);
        }

        // Đặt trạng thái đơn hàng thành 'pending'
        $order->update(['payment_status' => 'pending']);

        try {
            // Tạo phiên thanh toán Stripe mới
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
            // dd($stripe);
            $response = $stripe->checkout->sessions->create([
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => $order->currency ?? 'usd',
                            'product_data' => ['name' => "Edunity Courses"],
                            'unit_amount' => $order->total_price,
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => config('services.frontend_url') . '/checkout/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => config('services.frontend_url') . '/checkout/cancel',
            ]);

            // Cập nhật mã phiên thanh toán mới trong đơn hàng
            $order->update(['payment_code' => $response->id]);

            return response()->json([
                'status' => 'success',
                'message' => 'Order restored successfully. Proceed to payment.',
                'checkout_url' => $response->url,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create a new checkout session: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create a new checkout session',
            ], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return response()->json(['status' => 'error', 'message' => 'Session ID is required'], 400);
        }

        $order = Order::where('payment_code', $sessionId)->first();

        if ($order && $order->payment_status === 'paid') {
            return response()->json(['status' => 'success', 'payment_status' => $order->payment_status]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Payment not completed'], 400);
        }
    }

    public function handleWebhook(Request $request)
    {
        $endpoint_secret = config('services.stripe.webhook_secret');
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);

            if ($event->type === 'checkout.session.completed') {
                $session = $event->data->object;
                $order = Order::where('payment_code', $session->id)->first();

                if ($order && $order->payment_status === 'pending') {
                    $order->update(['payment_status' => 'paid']);
                    Log::info("Order #{$order->id} has been marked as paid.");
                } elseif (!$order) {
                    Log::warning("Order not found for session ID: {$session->id}");
                }
            } elseif ($event->type === 'checkout.session.expired') {
                $session = $event->data->object;
                $order = Order::where('payment_code', $session->id)->first();

                if ($order && $order->payment_status === 'pending') {
                    $order->update(['payment_status' => 'cancelled']);
                    Log::info("Order #{$order->id} has been cancelled due to expired session.");
                } elseif (!$order) {
                    Log::warning("Order not found for session ID: {$session->id}");
                }
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload');
            return response()->json(['status' => 'error', 'message' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid signature');
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        }
    }
}
