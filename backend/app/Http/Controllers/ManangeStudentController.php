<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;


class ManangeStudentController extends Controller
{
    public function getStudentsByTeacher(Request $request)
    {
        // Lấy ID của giáo viên đang đăng nhập
        $teacherId = auth()->user()->id;

        // Lấy tham số phân trang và điều kiện lọc
        $perPage = (int) $request->get('per_page', 10); // Số lượng bản ghi mỗi trang
        $page = (int) $request->get('page', 1); // Trang hiện tại
        $courseId = $request->get('course_id'); // ID của khóa học cần lọc

        // Tạo query để lấy danh sách học viên
        $query = User::select(
            'users.id',
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.phone_number',
            'courses.title as course_title',
            'orders.payment_status',
            'orders.created_at as purchase_date'
        )
            ->join('orders', 'users.id', '=', 'orders.user_id') // Liên kết học viên với đơn hàng
            ->join('order_items', 'orders.id', '=', 'order_items.order_id') // Liên kết đơn hàng với order item
            ->join('courses', 'order_items.course_id', '=', 'courses.id') // Liên kết order item với khóa học
            ->where('courses.created_by', $teacherId) // Khóa học do giáo viên tạo
            ->where('orders.payment_status', 'paid'); // Lọc thanh toán đã hoàn tất

        // Thêm điều kiện lọc theo course_id nếu được truyền vào
        if ($courseId) {
            $query->where('courses.id', $courseId);
        }

        // Lấy tất cả dữ liệu từ query
        $students = $query->get();

        // Tổng số học viên
        $total = $students->count();

        // Phân trang theo $page và $perPage
        $paginatedStudents = $students->forPage($page, $perPage)->values();

        // Tạo LengthAwarePaginator
        $pagination = new LengthAwarePaginator(
            $paginatedStudents,
            $total,
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        // Chuyển dữ liệu phân trang thành mảng
        $students = $pagination->toArray();

        // Trả về kết quả
        return formatResponse(
            STATUS_OK,
            $students,
            '',
            __('messages.student_fetch_success')
        );
    }

}
