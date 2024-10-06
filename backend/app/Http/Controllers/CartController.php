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

        // Lấy người dùng đang đăng nhập
        $user = auth()->user();

        // Lấy giỏ hàng của người dùng
        $cart = $user->cart;

        // Nếu người dùng chưa có giỏ hàng, tạo giỏ hàng mới
        if (!$cart) {
            $cart = $user->cart()->create();
        }

        // Thêm khóa học vào cart_items với cart_id đã được lấy
        $cartItem = $cart->cartItems()->create([
            'course_id' => $request->input('course_id'),
            'price' => $request->input('price'),
            'cart_id' => $cart->id,  // Đảm bảo gán cart_id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Khóa học đã được thêm vào giỏ hàng',
            'data' => $cartItem
        ], 200);
    }

    // Hàm lấy các khóa học trong giỏ hàng
    public function getCartItems()
    {
        $user = auth()->user();
        $cart = $user->cart;

        if (!$cart) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Không tìm thấy giỏ hàng',
            ], 404);
        }

        $cartItems = $cart->cartItems;

        return response()->json([
            'status' => 'success',
            'data' => $cartItems
        ], 200);
    }

    // Hàm xóa khóa học khỏi giỏ hàng
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

        $cartItem = CartItem::find($cartItemId);
        if (!$cartItem) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Không tìm thấy mục giỏ hàng'
            ], 404);
        }

        $cartItem->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Khóa học đã được xóa khỏi giỏ hàng'
        ], 200);
    }
}
