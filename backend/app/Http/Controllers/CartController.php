<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\User;

class CartController extends Controller
{
    // Hàm thêm khóa học vào giỏ hàng
    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|integer|exists:courses,id',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 400);
        }

        $user = auth()->user();
        $cart = $user->cart;

        if (!$cart) {
            // Nếu người dùng chưa có giỏ hàng, tạo giỏ hàng mới
            $cart = $user->cart()->create();
        }

        // Kiểm tra xem khóa học đã tồn tại trong giỏ hàng và bị xóa mềm hay không
        $existingCartItem = $cart->cartItems()->withTrashed()->where('course_id', $request->input('course_id'))->first();

        if ($existingCartItem) {
            if ($existingCartItem->trashed()) {
                // Nếu khóa học đã bị xóa mềm, khôi phục nó
                $existingCartItem->restore();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Khóa học đã được khôi phục vào giỏ hàng',
                    'data' => $existingCartItem,
                ], 200);
            }

            return response()->json([
                'status' => 'fail',
                'message' => 'Khóa học này đã tồn tại trong giỏ hàng của bạn',
            ], 400);
        }

        // Thêm khóa học mới vào giỏ hàng nếu chưa tồn tại
        $cartItem = $cart->cartItems()->create([
            'course_id' => $request->input('course_id'),
            'price' => $request->input('price'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Khóa học đã được thêm vào giỏ hàng',
            'data' => $cartItem,
        ], 200);
    }

    // Hàm lấy các khóa học trong giỏ hàng
    public function getCartItems()
    {
        // Lấy người dùng hiện tại
        $user = auth()->user();

        // Kiểm tra xem người dùng có giỏ hàng không
        if (!$user || !$user->cart) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Không tìm thấy giỏ hàng',
            ], 404);
        }

        // Lấy tất cả các mục trong giỏ hàng (bao gồm các mục chưa bị xóa mềm)
        $cartItems = $user->cart->cartItems()->whereNull('deleted_at')->get();

        // Kiểm tra xem giỏ hàng có mục nào không
        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Giỏ hàng trống',
            ], 200); // Trả về 200 với thông báo là giỏ hàng trống
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy danh sách khóa học trong giỏ hàng thành công',
            'data' => $cartItems,
        ], 200);
    }

    public function removeFromCart($cartItemId)
    {
        $user = auth()->user();
        $cart = $user->cart;

        if (!$cart) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Không tìm thấy giỏ hàng',
            ], 404);
        }

        // Tìm cartItem theo id
        $cartItem = $cart->cartItems()->where('id', $cartItemId)->first();

        if (!$cartItem) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Không tìm thấy khóa học trong giỏ hàng',
            ], 404);
        }

        // Thực hiện xóa mềm
        $cartItem->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Khóa học đã được xóa khỏi giỏ hàng (xóa mềm)',
        ], 200);
    }
}
