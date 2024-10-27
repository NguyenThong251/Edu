<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;

class WebhookController extends Controller
{
    public function handlePaymentWebhook(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $endpointSecret = config('services.stripe.webhook_secret');

        // Xác minh webhook
        try {
            $payload = $request->getContent();
            $sigHeader = $request->header('Stripe-Signature');
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);

            // Xử lý sự kiện payment_intent.succeeded
            if ($event->type === 'payment_intent.succeeded') {
                $paymentIntent = $event->data->object;

                // Lấy đơn hàng dựa trên payment_code
                $order = Order::where('payment_code', $paymentIntent->id)->first();

                if ($order) {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'active'
                    ]);

                    Log::info('Order payment succeeded', ['order_id' => $order->id]);
                } else {
                    Log::warning('Order not found for payment intent', ['payment_intent_id' => $paymentIntent->id]);
                }
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload');
            return response()->json(['status' => 'error', 'message' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Signature verification failed');
            return response()->json(['status' => 'error', 'message' => 'Signature verification failed'], 400);
        }
    }
}
