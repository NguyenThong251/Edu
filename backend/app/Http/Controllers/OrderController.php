<?php

namespace App\Http\Controllers;

use App\Models\{Cart, CartItem, Order, OrderItem};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Log};
use Illuminate\Support\Str;
use Stripe;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // Get user & user's cart
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();
        $cartItems = CartItem::where('cart_id', $cart->id)->get();
        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart is empty',
            ], 400);
        }

        // Calculate total price
        $total = round($cartItems->reduce(function ($carry, $item) {
            $price = $item->course->type_sale === 'percent'
                ? $item->course->price - ($item->course->price * $item->course->sale_value / 100)
                : $item->course->price - $item->course->sale_value;
            return $carry + $price;
        }, 0));
        $currency = $request->currency ?? 'usd';
        if ($currency == 'usd') {
            $total = $total * 100;
        }

        // TODO: Handle voucher if applicable
        $voucher = $request->voucher;

        try {
            DB::beginTransaction();

            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
            $response = $stripe->checkout->sessions->create([
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => $currency,
                            'product_data' => [
                                'name' => 'Edunity Courses',
                            ],
                            'unit_amount' => $total,
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => config('services.frontend_url') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => config('services.frontend_url'),
            ]);

            // Create order in the database
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
            Log::error($e);

            return response()->json([
                'status' => 'error',
                'message' => 'Order creation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session ID is required',
            ], 400);
        }

        // Truy vấn đơn hàng bằng mã session ID (payment_code)
        $order = Order::where('payment_code', $sessionId)->first();

        if ($order) {
            return response()->json([
                'status' => 'success',
                'payment_status' => $order->payment_status,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found',
            ], 404);
        }
    }

    public function handleWebhook(Request $request)
    {
        $endpoint_secret = config('services.stripe.webhook_secret');
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );

            if ($event->type === 'checkout.session.completed') {
                $session = $event->data->object;

                // Update order payment status to "paid"
                $order = Order::where('payment_code', $session->id)->first();
                if ($order) {
                    $order->update(['payment_status' => 'paid']);
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
