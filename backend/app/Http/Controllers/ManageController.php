<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order;

class ManageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    // Lấy tất cả user có role là 'admin'
    public function getAdmins(Request $request) {
        $perPage = $request->input('per_page', 10); // Số lượng admin trên mỗi trang, mặc định là 10
        $page = $request->input('page', 1); // Trang hiện tại, mặc định là trang 1

        $admins = User::where('role', 'admin')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'data' => $admins->items(),
            'pagination' => [
                'total' => $admins->total(), // Tổng số user
                'current_page' => $admins->currentPage(), // Trang hiện tại
                'last_page' => $admins->lastPage(), // Trang cuối cùng
                'per_page' => $admins->perPage(), // Số lượng user trên mỗi trang
            ],
        ]);
    }

    //Sửa tài khoản role "admin"
    public function updateUserAccount(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $id,
        ]);
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'fail',
                'message' => 'User không tồn tại',
            ], 404);
        }
        // Update email
        $user->email = $request->input('email');
        // Lưu vào db
        $user->save();
        // In phản hồi
        return response()->json([
            'status' => 'success',
            'message' => 'Tài khoản đã được cập nhật thành công',
            'data' => $user,
        ]);
    }

    //Sửa, thêm thông tin nền tảng user role "admin"
    public function updateFoundationAccount(Request $request, $id){
        $request->validate([
            'name' => 'required|string|max:511',
            'biography' => 'nullable|string',
            'phone_number' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);
        $nameParts = explode(' ', trim($request->input('name')));
        $firstName = array_shift($nameParts); // Lấy phần tử đầu tiên làm first_name
        $lastName = implode(' ', $nameParts); // Các phần tử còn lại làm last_name

        $user = User::find($id);

        if (!$user) {
            return response()->json([
               "status" => "fail",
               "message" =>"Tài khoản không tồn tại",
            ],404);
        }

        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->biography = $request->input('biography', $user->biography);
        $user->phone_number = $request->input('phone_number', $user->phone_number);
        $user->address = $request->input('address', $user->address);
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $fileName); // Di chuyển file vào thư mục images
            $user->background_image = $fileName;
        }
        $user->save();
        return response()->json([
            'status' => 'success',
            'message' => $id ? 'Thông tin user đã được cập nhật thành công' : 'Tài khoản này không tồn tại',
            'data' => $user,
        ]);
    }

    //Sửa, thêm thông tin liên lạc
    public function updateContactInfo(Request $request, $id)
    {
        $request->validate([
            'facebook' => 'nullable|string|url',
            'linkedin' => 'nullable|string|url',
        ]);

        // Tìm user theo ID
        $user = User::find($id);

        if ($user) {
            $contactInfo = $user->contact_info ?: [];

            if ($request->has('facebook')) {
                $contactInfo['facebook'] = $request->input('facebook');
            }

            if ($request->has('linkedin')) {
                $contactInfo['linkedin'] = $request->input('linkedin');
            }

            $user->contact_info = $contactInfo;

            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Thông tin liên lạc đã được cập nhật',
                'data' => $user->contact_info,
            ]);
        }
        return response()->json(['status' => 'error', 'message' => 'User không tồn tại'], 404);
    }

    //Delete user follow id
    public function delUserAdmin($id)
    {
        $user = User::find($id);

        if (!$user) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.user_not_found'));
        }
        $user->save();
        $user->delete();

        return formatResponse(STATUS_OK, '', '', __('messages.user_soft_delete_success'));
    }

    //Report Payment
    public function getAdminRpPayment()
    {
        $userId = Auth::id();

        // Truy vấn các đơn hàng dựa trên user_id
        $orders = Order::where('user_id', $userId)
            ->with(['orderItems.course']) // Sử dụng Eloquent để lấy thông tin khóa học
            ->get();

        $result = $orders->map(function($order) {
            return [
                'id' => $order->id,
                'total_price' => $order->total_price,
                'admin_revenue' => $order->total_price, // giả định doanh thu admin là tổng số tiền
                'created_at' => $order->created_at,
                'courses' => $order->orderItems->map(function($item) {
                    return $item->course->name; // Lấy tên của khóa học
                }),
            ];
        });

        return response()->json([
            $result,
            'status' => 'success',
        ]);
    }
    public function getInstructorRp()
    {
        // Truy vấn các đơn hàng dựa trên user_id
        $instructor = User::where('role', 'instructor')
        ->with(['orders.orderItems.course']) // Lấy tất cả các đơn hàng và khóa học liên quan
        ->get();

        $result = $instructor->map(function($instructor) {
            return [
                'instructor_name' => $instructor->name,
                'instructor_email' => $instructor->email,
                'orders' => $instructor->orders->map(function($order) {
                    return [
                        'order_id' => $order->id,
                        'total_price' => $order->total_price,
                        'created_at' => $order->created_at,
                        'courses' => $order->orderItems->map(function($item) {
                            return $item->course->name;
                        })
                    ];
                })
            ];
        });

        return response()->json([
            $result,
            'status' => 'success',
        ]);
    }
}
