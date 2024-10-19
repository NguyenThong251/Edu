<?php

return [
    'welcome' => 'Chào mừng bạn đến với ứng dụng của chúng tôi!',
    'user_signup_success' => 'Đăng ký thành công.',
    'user_login_success' => 'Đăng nhập thành công.',
    'user_logout_success' => 'Đăng xuất thành công.',
    'login_google_success' => 'Đăng nhập google thất bại.',

    'validation_error' => 'Có lỗi xác thực.',
    // Message for rolecheck Middleware
    'no_permission' => 'Bạn không có quyền thực hiện hành động này.',
    // Message for JWT Middleware
    'token_invalid' => 'Token không hợp lệ',
    'token_expired' => 'Token đã hết hạn',
    'token_not_found' => 'Không tìm thấy Token',
    'create_token_failed' => 'Tạo token thất bại',

    'user_not_found' => 'Không tìm thấy người dùng',

    // Message for category
    'name_required' => 'Tên không được để trống.',
    'email_required' => 'Email không được để trống.',

    'first_name_required' => 'Tên không được để trống.',
    'first_name_string' => 'Tên phải là chuỗi.',
    'first_name_max' => 'Tên không được quá :max ký tự.',
    'last_name_required' => 'Họ không được để trống.',
    'last_name_string' => 'Họ phải là chuỗi.',
    'last_name_max' => 'Họ không được quá :max ký tự.',

    'password_required' => 'Mật khẩu không được để trống.',
    'password_string' => 'Mật khẩu phải là chuỗi.',
    'password_min' => 'Mật khẩu ít nhất :min ký tự.',
    'password_incorrect' => 'Mật khẩu không chính xác.',

    'role_required' => 'Vài trò không được để trống.',
    'role_in' => 'Vai trò không tồn tại.',
    'gender_invalid' => 'Giới tính không tồn tại.',
    'date_of_birth_invalid' => 'Phải đúng định YYYY-MM-DD.',


    'validation_error_role' => 'Không được phép vài trò này.',

    'url_not_found' => 'Không tìm thấy đường dẫn.',
    'get_url_ok' => 'Lấy đường dẫn thành công.',

    'verify_email_ok' => 'Xác thực tài khoản thành công.',


    'name_string' => 'Tên phải là chuỗi.',
    'email_string' => 'Email phải là chuỗi.',
    'email_email' => 'Email phải đúng định dạng email.',
    'name_max' => 'Tên không được quá :max ký tự.',
    'email_max' => 'Email không được quá :max ký tự.',
    'name_unique' => 'Tên phải là duy nhất.',
    'email_unique' => 'Email đã có người sử dụng.',
    'email_exist' => 'Email không tồn tại.',
    'email_not_verified' => 'Email không tồn tại.',
    'email_send_ok' => 'Gửi email thành công.',

    'description_string' => 'Mô tả phải là chuỗi.',
    'status_required' => 'Trạng thái không được để trống.',
    'status_invalid' => 'Trạng thái không hợp lệ.',
    'parent_id_invalid' => 'ID cha không hợp lệ.',

    'error_save' => 'Lưu không thành công.',
    'successful_save' => 'Lưu thành công.',
    'update_fail' => 'Cập nhật thành công.',
    'update_success' => 'Cập nhật thành công.',

    // Cart: handle courses in cart
    'cart_cleared' => 'Tất cả các khóa học đã được xóa khỏi giỏ hàng.',
    'cart_empty' => 'Giỏ hàng của bạn đang trống.',
    'cart_item_not_found' => 'Không tìm thấy mặt hàng trong giỏ hàng.',
    'course_already_in_cart' => 'Khóa học đã có trong giỏ hàng.',
    'course_added_success' => 'Khóa học đã được thêm thành công.',
    'course_removed_success' => 'Khóa học đã được xóa thành công.',
    'course_not_found_in_cart' => 'Khóa học không có trong giỏ hàng.',
];
