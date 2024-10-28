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

        // TODO Handle voucher later
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
                                'name' => $cartItems->map(function ($item) {
                                    return $item->course->title;
                                })->join(', '),
                            ],
                            'unit_amount' => $total,
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => route('orders.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('orders.cancel'),
            ]);

            dd($response);

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
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([
                'status' => 'error',
                'message' => 'Order creation failed',
            ], 500);
        }
    }

    public function success(Request $request)
    {
        if (!$request->session_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session ID is required',
            ], 400);
        }
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        $response = $stripe->checkout->sessions->retrieve($request->session_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Order completed successfully',
            'data' => $response,
        ], 201);
    }

    public function cancel(Request $request)
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Order cancelled',
        ], 400);
    }
}
