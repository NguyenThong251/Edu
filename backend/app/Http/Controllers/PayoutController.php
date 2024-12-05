<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\PayoutRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stripe\Exception\ApiErrorException;
use Stripe\Payout;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Checkout\Session as StripeSession;

class PayoutController extends Controller
{
    public function __construct()
    {
        $this->middleware(
            'auth:api',
            [
                'except' => [
                    'handleWebhook',
                ]
            ]
        );
    }

    public function index(Request $request)
    {
        $userId = auth()->id();

        $payoutRequests = PayoutRequest::query()
            ->where('user_id', $userId) // Lọc theo user_id
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    $request->input('start_date'),
                    $request->input('end_date')
                ]);
            })
            ->paginate(10);

        return formatResponse('OK', $payoutRequests);
    }

    public function report()
    {
        $userId = auth()->id();

        $pendingPayouts = PayoutRequest::where('user_id', $userId)
            ->whereIn('status', ['pending', 'processing'])
            ->sum('amount');
        $pendingPayouts = PayoutRequest::where('user_id', $userId)
            ->whereIn('status', ['pending', 'processing'])
            ->sum('amount');

        $totalRevenue = \App\Models\Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('courses', 'order_items.course_id', '=', 'courses.id')
            ->where('courses.created_by', $userId)
            ->where('orders.payment_status', 'paid')
            ->whereNull('orders.deleted_at')
            ->whereNull('order_items.deleted_at')
            ->sum('order_items.price');
        $moneyReceived = PayoutRequest::where(['user_id' => $userId, 'status' => 'paid'])->sum('amount');

        return formatResponse('OK', [
            'pendingPayouts' => $pendingPayouts,
            'totalRevenue' => $totalRevenue,
            'availablePayout' => $totalRevenue * 0.7 - $pendingPayouts,
            'moneyReceived' => $moneyReceived,
        ]);
    }


    public function requestPayout(Request $request)
    {

        //        Stripe::setApiKey(env('STRIPE_SECRET'));
        //        Stripe::setApiKey(config('services.stripe.secret'));

        ////        $balance = \Stripe\Balance::retrieve();
        //        $charge = \Stripe\Charge::create([
        //            'amount' => 1000, // 10 USD (tính bằng cents)
        //            'currency' => 'usd',
        //            'source' => 'tok_visa', // Token test
        //            'description' => 'Test charge to increase balance',
        //        ]);
        //
        //        // Chuyển tiền từ platform account đến connected account
        //        $transfer = \Stripe\Transfer::create([
        //            'amount' => 1000, // 10 USD
        //            'currency' => 'usd',
        //            'destination' => 'acct_1QOXVvCzrOkmIPq4', // ID của connected account
        //            'description' => 'Test transfer to connected account',
        //        ]);

        $user = Auth::user();
        // Kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:10',
            'currency' => 'required|string|in:usd,eur,vnd',
        ], [
            'amount.required' => __('messages.amount_required'),
            'amount.numeric' => __('messages.amount_numeric'),
            'amount.min' => __('messages.amount_min'),
            'currency.required' => __('messages.currency_required'),
            'currency.in' => __('messages.currency_invalid'),
        ]);

        if ($validator->fails()) {
            return formatResponse(STATUS_FAIL, '', $validator->errors(), __('messages.validation_error'), 400);
        }

        $amount = $request->input('amount');
        $currency = $request->input('currency');
        $reason = $request->input('reason');
        // Kiểm tra số dư khả dụng của user
        $availableBalance = $this->calculateAvailableBalance($user->id);

        if ($amount > $availableBalance) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.amount_exceeds_balance'), 400);
        }

        // Lấy phương thức thanh toán mặc định (Stripe nếu có)
        $paymentMethod = PaymentMethod::where('user_id', $user->id)->where('status', 'active')->first();

        if (!$paymentMethod) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.payment_method_not_found'), 400);
        }
        $payoutRequest = PayoutRequest::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'pending',
            'reason' => $reason ?? null,
        ]);
        return formatResponse(STATUS_OK, $payoutRequest, '', __('messages.payout_request_created'), 200);
    }

    private function calculateAvailableBalance($userId)
    {
        //Tính từ tổng doanh thu đã bán - đã rút
        $totalRevenue = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('courses', 'order_items.course_id', '=', 'courses.id')
            ->where('courses.created_by', $userId)
            ->where('orders.payment_status', 'paid')
            ->whereNull('orders.deleted_at')
            ->whereNull('order_items.deleted_at')
            ->sum('order_items.price');

        // phần trăm giáo viên 70%
        $sharePercentage = 70;
        $availableBalance = ($totalRevenue * $sharePercentage) / 100;

        // Trừ đi các yêu cầu rút tiền đã được duyệt nhưng chưa hoàn thành
        $pendingPayouts = PayoutRequest::where('user_id', $userId)
            ->whereIn('status', ['pending', 'processing'])
            ->sum('amount');
        return $availableBalance - $pendingPayouts;
    }

    //admin xử lý
    public function processPayout(Request $request, $id)
    {
        // Tìm yêu cầu rút tiền
        $payoutRequest = PayoutRequest::find($id);
        if (!$payoutRequest) {
            return response()->json([
                'status' => 'FAIL',
                'data' => '',
                'error' => __('messages.payout_request_not_found'),
                'message' => __('messages.payout_request_not_found'),
                'code' => 404
            ], 404);
        }

        if ($payoutRequest->status !== 'pending') {
            return response()->json([
                'status' => 'FAIL',
                'data' => '',
                'error' => __('messages.payout_request_not_pending'),
                'message' => __('messages.payout_request_not_pending'),
                'code' => 400
            ], 400);
        }

        // Lấy phương thức thanh toán của user (Stripe nếu có)
        $paymentMethod = PaymentMethod::join('users', 'payment_methods.user_id', '=', 'users.id')
            ->where('payment_methods.user_id', $payoutRequest->user_id)
            ->where('payment_methods.type', 'stripe')
            ->where('payment_methods.status', 'active')
            ->select('payment_methods.*', 'users.first_name', 'users.last_name')
            ->first();

        if (!$paymentMethod) {
            return response()->json([
                'status' => 'FAIL',
                'data' => '',
                'error' => __('messages.stripe_not_linked'),
                'message' => __('messages.stripe_not_linked'),
                'code' => 400
            ], 400);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Tạo Stripe Checkout Session
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $payoutRequest->currency,
                        'product_data' => [
                            'name' => 'Payout for User: ' . $paymentMethod->first_name . " " . $paymentMethod->last_name,
                        ],
                        'unit_amount' => $payoutRequest->amount = ($payoutRequest->currency === 'usd') ? $payoutRequest->amount * 100 : $payoutRequest->amount, // Tính bằng cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => config('services.frontend_url') . 'payout/process-request/success',
                'cancel_url' => config('services.frontend_url') . 'payout/process-request/cancel',
                'metadata' => [
                    'type' => 'payout',
                    'payout_request_id' => $payoutRequest->id,
                ],
            ]);

            $payoutRequest->update([
                'status' => 'processing',
            ]);

            return response()->json([
                'status' => 'SUCCESS',
                'data' => [
                    'transaction_link' => $session->url,
                    'payout_request' => $payoutRequest,
                ],
                'message' => __('messages.payout_processing'),
                'code' => 200
            ], 200);
        } catch (\Exception $e) {
            Log::error('Stripe Checkout Session Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'FAIL',
                'data' => '',
                'error' => 'Stripe Error: ' . $e->getMessage(),
                'message' => __('messages.payout_failed'),
                'code' => 500
            ], 500);
        }
    }


    //admin
    public function listPayoutRequests(Request $request)
    {
        $admin = Auth::user();
        // Lấy các yêu cầu rút tiền với phân trang
        $payoutRequests = PayoutRequest::with('user', 'user.paymentMethods')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return formatResponse(STATUS_OK, $payoutRequests, '', __('messages.payout_requests_list'), 200);
    }

    public function rejectRequest(Request $request, $id)
    {
        $reason = $request->input('reason');
        $payoutRequest = PayoutRequest::find($id);
        if (!$payoutRequest) {
            return response()->json([
                'status' => 'FAIL',
                'data' => '',
                'message' => 'Payout request not found',
                'code' => 404
            ], 404);
        }

        if ($payoutRequest->status !== 'pending') {
            return response()->json([
                'status' => 'FAIL',
                'data' => '',
                'error' => '',
                'message' => 'Payout request not status pending',
                'code' => 400
            ], 400);
        }

        if (!$payoutRequest->update(['status' => 'rejected', 'reason' => $reason ?? null])) {
            return response()->json([
                'status' => 'FAIL',
                'data' => '',
                'error' => '',
                'message' => 'Rejected Payout Request failed.',
                'code' => 400
            ]);
        }
        return response()->json([
            'status' => 'SUCCESS',
            'data' => $payoutRequest,
            'message' => 'Payout request rejected.',
        ]);
    }


    //    public function handleWebhook(Request $request)
    //    {
    //        $payload = $request->getContent();
    //        $sig_header = $request->header('Stripe-Signature');
    //        $endpoint_secret = config('services.stripe.webhook_secret');
    //
    //        try {
    //            $event = Webhook::constructEvent(
    //                $payload, $sig_header, $endpoint_secret
    //            );
    //        } catch (\UnexpectedValueException $e) {
    //            // Invalid payload
    //            return response()->json(['error' => 'Invalid payload'], 400);
    //        } catch (\Stripe\Exception\SignatureVerificationException $e) {
    //            // Invalid signature
    //            return response()->json(['error' => 'Invalid signature'], 400);
    //        }
    //
    //        // Xử lý sự kiện
    //        switch ($event->type) {
    //            case 'checkout.session.completed':
    //                $session = $event->data->object;
    //
    //                // Lấy payout_request_id từ metadata
    //                $payoutRequestId = $session->metadata->payout_request_id;
    //                $payoutRequest = PayoutRequest::find($payoutRequestId);
    //
    //                if ($payoutRequest && $payoutRequest->status == 'processing') {
    //                    $payoutRequest->update([
    //                        'status' => 'paid',
    ////                        'transaction_id' => $session->payment_intent, // Hoặc thông tin liên quan khác
    //                    ]);
    //                }
    //                break;
    //
    //            case 'checkout.session.expired':
    //                $session = $event->data->object;
    //                $payoutRequestId = $session->metadata->payout_request_id;
    //                $payoutRequest = PayoutRequest::find($payoutRequestId);
    //
    //                if ($payoutRequest && $payoutRequest->status == 'processing') {
    //                    $payoutRequest->update([
    //                        'status' => 'failed',
    //                        'reason' => 'Checkout session expired',
    //                    ]);
    //                }
    //                break;
    //
    //            // Xử lý các sự kiện khác nếu cần
    //            default:
    //                Log::info('Unhandled event type ' . $event->type);
    //        }
    //        return response()->json(['status' => 'success'], 200);
    //    }
}
