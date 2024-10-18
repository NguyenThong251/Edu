<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
        ], [
            'course_id.required' => 'Required',
            'course_id.exists' => __('messages.course_not_exists'),
        ]);

        // Nếu xác thực thất bại
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => __('messages.validation_error'),
                'errors' => $validator->errors(),
            ], CODE_FAIL);  // 400 Bad Request
        }

        // TEST
        // Lấy người dùng hiện tại, nếu không có, dùng người dùng mặc định với id = 1
        //$user = Auth::user() ?? User::find(1);

        // Lấy người dùng hiện tại
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'fail',
                'message' => __('messages.user_not_found'),
            ], CODE_NOT_FOUND);  // 404 Not Found
        }

        // Lấy thông tin khóa học
        $course = Course::findOrFail($request->course_id);

        // Tìm hoặc tạo giỏ hàng cho người dùng
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // Kiểm tra lại xem giỏ hàng đã được tạo thành công chưa
        if (!$cart || !$cart->id) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Failed to create or retrieve cart',
            ], CODE_BAD);  // 500 Internal Server Error
        }

        // Kiểm tra xem khóa học đã có trong giỏ hàng chưa
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('course_id', $course->id)
            ->first();

        if ($cartItem) {
            return response()->json([
                'status' => 'fail',
                'message' => __('messages.course_exists'),
            ], 409);  // 409 Conflict
        }

        // Thêm khóa học vào giỏ hàng với giá
        CartItem::create([
            'cart_id' => $cart->id,
            'course_id' => $course->id,
            'price' => $course->price,  // Thêm giá của khóa học
        ]);

        return response()->json([
            'status' => 'success',
            'message' => __('messages.add_to_cart_successfully'),
        ], CODE_OK);
    }
}
