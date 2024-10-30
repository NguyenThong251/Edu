<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $cart = Cart::getOrCreateForUser($user);
            $courses = $cart->getFormattedItems();

            return $this->formatResponse('success', __('messages.cart_items_fetched'), $courses);
        } catch (\Exception $e) {
            return $this->formatResponse('error', $e->getMessage(), null, $e->getCode() ?: 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'course_id' => 'required|exists:courses,id',
            ]);

            $user = Auth::user();
            $cart = Cart::getOrCreateForUser($user);

            if ($cart->isCourseInPaidOrder($validatedData['course_id'], $user->id)) {
                return $this->formatResponse('error', __('messages.course_already_in_paid_order'), null, 400);
            }

            DB::beginTransaction();
            $existingCartItem = $cart->cartItems()->where('course_id', $validatedData['course_id'])->first();
            if ($existingCartItem) {
                throw new \Exception(__('messages.course_already_in_cart'), 400);
            }

            $cart->cartItems()->create([
                'course_id' => $validatedData['course_id'],
            ]);

            DB::commit();

            $courses = $cart->getFormattedItems();
            return $this->formatResponse('success', __('messages.course_added_success'), $courses, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->formatResponse('error', 'Validation Error', $e->errors(), 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->formatResponse('error', $e->getMessage(), null, $e->getCode() ?: 500);
        }
    }

    public function destroy($course_id)
    {
        try {
            $user = Auth::user();
            $cart = Cart::getOrCreateForUser($user);
            $cartItem = $cart->cartItems()->where('course_id', $course_id)->first();

            if (!$cartItem) {
                return $this->formatResponse('error', __('messages.course_not_found_in_cart'), null, 404);
            }

            $cartItem->delete();
            $courses = $cart->getFormattedItems();
            return $this->formatResponse('success', __('messages.course_removed_success'), $courses);
        } catch (\Exception $e) {
            return $this->formatResponse('error', $e->getMessage(), null, $e->getCode() ?: 500);
        }
    }

    public function destroyAll()
    {
        try {
            $user = Auth::user();
            $cart = Cart::getOrCreateForUser($user);
            $cart->clearCart();

            return $this->formatResponse('success', __('messages.cart_cleared'));
        } catch (\Exception $e) {
            return $this->formatResponse('error', $e->getMessage(), null, $e->getCode() ?: 500);
        }
    }

    private function formatResponse($status, $message, $data = null, $code = 200)
    {
        $response = [
            'status' => $status,
            'message' => $message,
        ];

        if ($data) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }
}
