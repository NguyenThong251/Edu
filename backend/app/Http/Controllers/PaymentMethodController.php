<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Exception;
use Stripe\Balance;
use Stripe\Charge;
use Stripe\Exception\ApiErrorException;
use Stripe\OAuth;
use Stripe\Stripe;

class PaymentMethodController extends Controller
{
    public function linkStripe()
    {
        $user = Auth::user();

        // Tạo payload cho JWT
        $payload = [
            'user_id' => $user->id,
            'exp' => now()->addMinutes(10)->timestamp, // Hạn sử dụng 10 phút
        ];
        $jwt = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        $stripeClientId = env('STRIPE_CLIENT_ID');
        $redirectUri = env('URL_BASE_PUBLIC_BE') . "/api/auth/payment-methods/stripe/callback";
        $url = "https://connect.stripe.com/oauth/authorize?response_type=code&client_id={$stripeClientId}&scope=read_write&redirect_uri={$redirectUri}&state={$jwt}";
        return formatResponse(STATUS_OK, ['url' => $url], '', __('messages.stripe_link_url'));
    }

    /**
     * Xử lý callback từ Stripe sau khi liên kết.
     */
    public function handleStripeCallback(Request $request)
    {
        $redirectUrl = env('URL_DOMAIN') . "/teacher/payment-method";

        $code = $request->input('code');
        $state = $request->input('state');

        if (!$code || !$state) {
            return redirect($redirectUrl . "/fail");
//            return formatResponse(STATUS_FAIL, '', '', __('messages.stripe_invalid_code'));
        }
        try {
            $decoded = JWT::decode($state, new Key(env('JWT_SECRET'), 'HS256'));
            $user = User::find($decoded->user_id);
            if (!$user) {
                return redirect($redirectUrl . "/fail");
//                return formatResponse(STATUS_FAIL, '', '', 'user_not_found');
            }
            if ($decoded->exp < now()->timestamp) {
                return redirect($redirectUrl . "/fail");
//                return formatResponse(STATUS_FAIL, '', '', 'stripe_state_expired');
            }
        } catch (\Exception $e) {
            return redirect($redirectUrl . "/fail");
//            return formatResponse(STATUS_FAIL, '', $e->getMessage(), 'stripe_invalid_state');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Trao đổi code lấy access token
            $response = OAuth::token([
                'grant_type' => 'authorization_code',
                'code' => $code,
            ]);
            $existingStripe = PaymentMethod::where('user_id', $user->id)
                ->where('type', 'stripe')
                ->where('status', 'active')
                ->first();
            if ($existingStripe) {
                return redirect($redirectUrl . "/fail");
//                return formatResponse(STATUS_FAIL, '', '', __('messages.stripe_already_linked'));
            }

            PaymentMethod::create([
                'user_id' => $user->id,
                'type' => 'stripe',
                'details' => [
                    'stripe_account_id' => $response->stripe_user_id,
                    'scope' => $response->scope,
                    'token_type' => $response->token_type,
                    'refresh_token' => $response->refresh_token,
                ],
                'account_info_number' => $response->stripe_user_id,
                'status' => 'active',
            ]);
            return redirect($redirectUrl . "/success");
//            return formatResponse(STATUS_OK, '', '', 'Stripe account linked successfully.');
        } catch (\Exception $e) {
            return redirect($redirectUrl . "/fail");
//            return formatResponse(STATUS_FAIL, '', $e->getMessage(), __('messages.stripe_link_fail'));
        }
    }

    /**
     * Liệt kê các phương thức thanh toán của user.
     */
    public function listPaymentMethods()
    {
        $user = Auth::user();
        $paymentMethods = PaymentMethod::where('user_id', $user->id)->get();
        return formatResponse(STATUS_OK, $paymentMethods, '', __('messages.payment_methods_list'));
    }

    /**
     * Thêm các phương thức thanh toán khác (ví dụ: PayPal, Bank Transfer, ...)
     */
    public function addPaymentMethod(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:paypal,bank_transfer,momo,vnpay',
            'details' => 'required|array',
        ], [
            'type.required' => __('messages.payment_method_type_required'),
            'type.in' => __('messages.payment_method_type_invalid'),
            'details.required' => __('messages.payment_method_details_required'),
            'details.array' => __('messages.payment_method_details_invalid'),
        ]);
        if ($validator->fails()) {
            return formatResponse(STATUS_FAIL, '', $validator->errors(), __('messages.validation_error'));
        }
        $type = $request->input('type');
        $details = $request->input('details');
        $existingMethod = PaymentMethod::where('user_id', $user->id)->where('type', $type)->first();
        if ($existingMethod) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.payment_method_already_exists'));
        }
        $paymentMethod = PaymentMethod::create([
            'user_id' => $user->id,
            'type' => $type,
            'details' => $details,
            'status' => 'active',
        ]);
        return formatResponse(STATUS_OK, $paymentMethod, '', __('messages.payment_method_added'));
    }

    /**
     * Xóa phương thức thanh toán.
     */
    public function deletePaymentMethod($id)
    {
        $user = Auth::user();
        $paymentMethod = PaymentMethod::where('user_id', $user->id)->where('id', $id)->first();
        if (!$paymentMethod) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.payment_method_not_found'));
        }
        $paymentMethod->delete();
        return formatResponse(STATUS_OK, '', '', __('messages.payment_method_deleted'));
    }

//    public function test()
//    {
//        Stripe::setApiKey(config('services.stripe.secret'));
//
//        try {
//            $balance = Balance::retrieve();
//            // Hiển thị thông tin số dư
//            return response()->json([
//                'status' => 'SUCCESS',
//                'data' => $balance,
//                'message' => 'Balance retrieved successfully',
//                'code' => 200
//            ], 200);
//        } catch (\Exception $e) {
//            Log::error('Stripe Balance Error: ' . $e->getMessage());
//            return response()->json([
//                'status' => 'FAIL',
//                'data' => '',
//                'error' => 'Stripe Error: ' . $e->getMessage(),
//                'message' => __('messages.balance_retrieval_failed'),
//                'code' => 500
//            ], 500);
//        }

//        // Thông tin giao dịch thử nghiệm
//        $amount = 5000; // Số tiền (ví dụ: 2000 = $20.00)
//        $currency = 'usd';
//        $source = 'tok_visa'; // Token test của Stripe
//        $description = 'Test Transaction nèeeeeeeee';
//        $connectedAccountId = 'acct_1Example12345'; // Thay bằng Account ID của bạn
//
//        try {
//            // Tạo một Charge trên tài khoản chủ (Platform)
//            $charge = Charge::create([
//                'amount' => $amount,
//                'currency' => $currency,
//                'source' => $source,
//                'description' => $description,
//                'on_behalf_of' => "acct_1QOXVvCzrOkmIPq4", // Đảm bảo tiền được định cư tại tài khoản kết nối
//                'transfer_data' => [
//                    'destination' => "acct_1QOXVvCzrOkmIPq4",
//                ],
//            ]);
//
//            // Trả về kết quả thành công
//            return response()->json([
//                'status' => 'success',
//                'charge_id' => $charge->id,
//                'amount' => $charge->amount,
//                'currency' => $charge->currency,
//                'description' => $charge->description,
//                'destination_account' => $connectedAccountId,
//            ], 200);
//
//
//        } catch (ApiErrorException $e) {
//            // Ghi log lỗi để dễ dàng debug
//            Log::error('Stripe Charge Error: ' . $e->getMessage());
//
//            // Trả về kết quả lỗi
//            return response()->json([
//                'status' => 'fail',
//                'message' => 'Giao dịch thất bại: ' . $e->getMessage(),
//            ], 500);
//        }
//    }

}
