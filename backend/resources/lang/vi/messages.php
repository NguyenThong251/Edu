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
    'category_fetch_success' => 'Lấy danh mục thành công.',
    'category_not_found' => 'Không tìm thấy danh mục',
    'category_detail_success' => 'Lấy thông tin danh mục thành công.',
    'category_create_success' => 'Danh mục đã được tạo thành công.',
    'category_update_success' => 'Danh mục đã được cập nhật thành công.',
    'category_soft_delete_success' => 'Danh mục đã được xóa thành công.',
    'category_restore_success' => 'Danh mục đã được khôi phục thành công.',
    'category_force_delete_success' => 'Danh mục đã được xóa vĩnh viễn thành công.',
    'category_not_deleted' => 'Danh mục chưa bị xóa.',

    //Message of Course
    'course_not_found' => 'Không tìm thấy khóa học.',
    'course_detail_success' => 'Lấy thông tin khóa học thành công.',
    'course_create_success' => 'Khóa học đã được tạo thành công.',
    'course_update_success' => 'Khóa học đã được cập nhật thành công.',
    'course_soft_delete_success' => 'Khóa học đã được xóa thành công.',
    'course_restore_success' => 'Khóa học đã được khôi phục thành công.',
    'course_force_delete_success' => 'Khóa học đã được xóa vĩnh viễn thành công.',
    'course_fetch_success' => 'Lấy danh sách khóa học thành công.',
    'title_required' => 'Tên khóa học là bắt buộc.',
    'title_unique' => 'Tên khóa học phải là duy nhất.',
    'category_id_required' => 'Mã danh mục là bắt buộc.',
    'category_id_invalid' => 'Mã danh mục không hợp lệ.',
    'level_id_required' => 'Mã cấp độ là bắt buộc.',
    'level_id_invalid' => 'Mã cấp độ không hợp lệ.',
    'thumbnail.required' => 'Hình ảnh là bắt buộc.',
    'thumbnail.image' => 'Tệp tải lên phải là hình ảnh.',
    'thumbnail.mimes' => 'Chỉ chấp nhận các định dạng hình ảnh: jpeg, png, jpg, gif, svg.',
    'thumbnail.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
    'price_required' => 'Giá là bắt buộc.',
    'type_sale_required' => 'Loại hình bán hàng là bắt buộc.',
    'not_your_course' => 'Đây không phải là khóa học của bạn.',
    'no_popular_courses' => 'Hiện không có khóa học phổ biến nào.',
    'popular_courses_found' => 'Đã lấy thành công các khóa học phổ biến.',
    'no_new_courses' => 'Không có khóa học nào mới.',
    'new_courses_found' => 'Đã lấy thành công các khóa học mới.',
    'top_rated_courses_found' => 'Đã lấy thành công các khóa học hàng đầu.',
    'no_top_rated_courses' => 'Không có khóa học nào được đánh giá cao.',
    'tag_new' => 'Mới nhất',
    'tag_top_rated' => 'Đánh giá cao',
    'tag_popular' => 'Phổ biến nhất',
    'tag_favorite' => 'Yêu thích nhất',

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

    // Admin user
    'getUsers' => 'Lấy dữ liệu thành công',
    'updateUser' => 'Cập nhật thành công',

];
