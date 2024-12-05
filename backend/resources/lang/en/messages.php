<?php

return [
    'welcome' => 'Welcome to our application!',
    'user_signup_success' => 'User register successful.',
    'user_login_success' => 'User login successful.',
    'user_logout_success' => 'User logout successful.',
    'login_google_success' => 'Google login failed.',

    'validation_error' => 'There were validation errors.',
    // Message for rolecheck Middleware
    'no_permission' => 'You do not have permission to perform this action.',
    // Message for JWT Middleware
    'token_invalid' => 'Invalid token.',
    'token_expired' => 'Token has expired.',
    'token_not_found' => 'Token not found.',
    'create_token_failed' => 'token generation failed.',
    'user_not_found' => 'User not found.',

    // Message for Validate Category
    'name_required' => 'Name is required.',
    'email_required' => 'Email is required.',
    'name_string' => 'Name must be a string.',
    'email_string' => 'Email must be a string.',
    'email_email' => 'Email must be in correct email format.',
    'email_max' => 'Email must not exceed :max characters',
    'email_send_ok' => 'Email sent successfully.',

    'first_name_required' => 'First name is required.',
    'first_name_string' => 'First name must be a string.',
    'first_name_max' => 'First name must not exceed :max characters',
    'last_name_required' => 'Last name is required.',
    'last_name_string' => 'Last name must be a string.',
    'last_name_max' => 'Last name must not exceed :max characters.',

    'password_required' => 'Password is required.',
    'password_string' => 'Password must be a string.',
    'password_min' => 'Password must be at least :min characters.',
    'password_incorrect' => 'Password is incorrect.',

    'role_required' => 'Role is required.',
    'role_in' => 'Role does not exist.',
    'gender_invalid' => 'Gender does not exist',
    'date_of_birth_invalid' => 'Must follow YYYY-MM-DD format.',

    'validation_error_role' => 'This role is not allowed to be created.',

    'url_not_found' => 'Url not found.',
    'get_url_ok' => 'Get path succeeded',
    'verify_email_ok' => 'Account Verification Successful.',


    'name_max' => 'Name may not be greater than :max characters.',
    'name_unique' => 'Name must be unique.',
    'email_unique' => 'Email has already been taken.',
    'email_exist' => 'Email does not exist.',
    'email_not_verified' => 'Email not verified.',

    'description_string' => 'Description must be a string.',
    'icon_string' => 'Icon must be a string.',
    'keyword_string' => 'Keyword must be a string.',
    'status_required' => 'Status is required.',
    'status_invalid' => 'Status is invalid.',
    'parent_id_invalid' => 'Parent ID is invalid.',
    'category_fetch_success' => 'Categories retrieved successfully.',
    'category_not_found' => 'Category not found',
    'category_detail_success' => 'Category details retrieved successfully.',
    'category_create_success' => 'Category created successfully.',
    'category_update_success' => 'Category updated successfully.',
    'category_soft_delete_success' => 'Category deleted successfully.',
    'category_restore_success' => 'Category restored successfully.',
    'category_force_delete_success' => 'Category permanently deleted successfully.',
    'category_not_deleted' => 'Category has not been deleted yet.',

    //Message of Course
    'course_not_found' => 'Course not found.',
    'course_detail_success' => 'Course details retrieved successfully.',
    'course_create_success' => 'Course created successfully.',
    'course_update_success' => 'Course updated successfully.',
    'course_soft_delete_success' => 'Course deleted successfully.',
    'course_restore_success' => 'Course restored successfully.',
    'course_force_delete_success' => 'Course permanently deleted successfully.',
    'course_fetch_success' => 'Courses retrieved successfully.',
    'title_required' => 'Title is required.',
    'title_unique' => 'Title must be unique.',
    'category_id_required' => 'Category is required.',
    'category_id_invalid' => 'Category does not exist.',
    'level_id_required' => 'Level is required.',
    'level_id_invalid' => 'Level does not exist.',
    'language_id_required' => 'Language is required.',
    'language_id_invalid' => 'Language does not exist.',
    'thumbnail_required' => 'Thumbnail is required.',
    'thumbnail_image' => 'The uploaded file must be an image.',
    'thumbnail_mimes' => 'Only image formats are accepted: jpeg, png, jpg, gif, svg.',
    'thumbnail_max' => 'Thumbnail size must not exceed 2MB.',
    'price_required' => 'Price is required.',
    'type_sale_required' => 'Sale type is required.',
    'language_required' => 'Language is required.',
    'status_required' => 'Status is required.',
    'not_your_course' => 'This is not your course.',
    'no_popular_courses' => 'There are no popular courses at the moment.',
    'popular_courses_found' => 'Popular courses retrieved successfully.',
    'new_courses_found' => 'New courses retrieved successfully.',
    'no_new_courses' => 'There are no new courses.',
    'top_rated_courses_found' => 'Top-rated courses retrieved successfully.',
    'no_top_rated_courses' => 'There are no top-rated courses.',
    'tag_new' => 'New',
    'tag_top_rated' => 'Top Rated',
    'tag_popular' => 'Popular',
    'tag_favorite' => 'Favorite',
    'status_required' => 'The status field is required.',
    'invalid_status' => 'The status is invalid. Only active or inactive values are allowed.',
    'course_status_update_success' => 'Course status updated successfully.',
    'validation_error' => 'There was an error during validation.',
    'title_required' => 'The title field is required.',
    'thumbnail_required' => 'The thumbnail field is required.',
    'thumbnail_image' => 'The thumbnail must be an image file.',
    'thumbnail_mimes' => 'The thumbnail must be a file of type: jpeg, png, jpg, gif, svg.',
    'thumbnail_max' => 'The thumbnail size must not exceed 2MB.',
    'price_required' => 'The price field is required.',
    'price_numeric' => 'The price must be a valid number.',
    'type_sale_required' => 'The type of sale field is required.',
    'type_sale_invalid' => 'The type of sale must be either percent or price.',
    'course_create_success' => 'Course created successfully.',
    'course_update_success' => 'Course updated successfully.',
    'course_not_found' => 'Course not found.',

    'error_save' => 'Save failed.',
    'successful_save' => 'Save successfully.',
    'update_fail' => 'Update failed.',
    'update_success' => 'Update successfully.',

    //Message for Course Level
    'course_level_fetch_success' => 'Course levels fetched successfully',
    'course_level_not_found' => 'Course level not found',
    'course_level_detail_success' => 'Course level details fetched successfully',
    'course_level_create_success' => 'Course level created successfully',
    'course_level_update_success' => 'Course level updated successfully',
    'course_level_soft_delete_success' => 'Course level soft-deleted successfully',
    'course_level_restore_success' => 'Course level restored successfully',
    'course_level_force_delete_success' => 'Course level permanently deleted successfully',
    'name_course_level_required' => 'The name is required',
    'name_course_level_string' => 'The name must be a string',
    'name_course_level_max' => 'The name may not be greater than 100 characters',
    'name_course_level_unique' => 'The name has already been taken',

    //Message for Language
    'name_language_required' => 'The language name is required.',
    'name_language_string' => 'The language name must be a string.',
    'name_language_max' => 'The language name may not be greater than 100 characters.',
    'name_language_unique' => 'The language name has already been taken.',
    'status_required' => 'The status is required.',
    'status_invalid' => 'The status is invalid.',
    'language_fetch_success' => 'Languages fetched successfully.',
    'language_detail_success' => 'Language detail fetched successfully.',
    'language_create_success' => 'Language created successfully.',
    'language_update_success' => 'Language updated successfully.',
    'language_soft_delete_success' => 'Language soft deleted successfully.',
    'language_restore_success' => 'Language restored successfully.',
    'language_force_delete_success' => 'Language permanently deleted successfully.',
    'language_not_found' => 'Language not found.',

    //Section
    'section_fetch_success' => 'Sections fetched successfully.',
    'section_create_success' => 'Section created successfully.',
    'section_update_success' => 'Section updated successfully.',
    'sections_order_update_success' => 'Section order updated successfully.',
    'section_soft_delete_success' => 'Section deleted successfully.',
    'section_restore_success' => 'Section restored successfully.',
    'section_force_delete_success' => 'Section permanently deleted successfully.',
    'section_not_found' => 'Section not found.',
    'course_id_required' => 'Course is required.',
    'course_id_invalid' => 'Not found course.',
    'title_required' => 'Title is required.',
    'status_required' => 'Status is required.',
    'status_invalid' => 'Invalid status.',
    'sections_fetch_success' => 'Sections fetched successfully.',
    'section_detail_success' => 'Section details retrieved successfully.',

    //Lecture
    'lecture_fetch_success' => 'Lecture list fetched successfully.',
    'lecture_detail_success' => 'Lecture details fetched successfully.',
    'lecture_create_success' => 'Lecture created successfully.',
    'lecture_update_success' => 'Lecture updated successfully.',
    'lecture_soft_delete_success' => 'Lecture soft deleted successfully.',
    'lecture_restore_success' => 'Lecture restored successfully.',
    'lecture_force_delete_success' => 'Lecture permanently deleted successfully.',
    'lecture_not_found' => 'Lecture not found.',
    'lecture_section_updated' => 'Lecture section updated successfully.',
    'lecture_status_updated' => 'Lecture status updated successfully.',
    'content_upload_failed' => 'Content upload failed.',
    'invalid_data_format' => 'Invalid data format.',
    'content_fetch_success' => 'Content list fetched successfully.',
    'order_update_success' => 'Order updated successfully.',
    'validation_error' => 'Invalid data provided.',
    'content_upload_failed' => 'Content upload failed.',
    'content_must_be_video' => 'Content must be a video file.',
    'content_must_be_pdf' => 'Content must be a PDF file.',
    'section_not_owned' => 'Section does not belong to you.',
    'unauthorized_action' => 'You are not authorized to perform this action.',
    'section_id_required' => 'Section is required.',
    'duration_required' => 'Duration is required.',
    'section_id_invalid' => 'Section is invalid.',
    'type_required' => 'Lecture type is required.',
    'type_invalid' => 'Lecture type is invalid.',
    'title_required' => 'Lecture title is required.',
    'title_max' => 'Lecture title must not exceed 255 characters.',
    'content_required' => 'Lecture content is required.',
    'content_file' => 'Content must be a file.',
    'content_mimes' => 'Content must be a file of type mp4 or pdf.',
    'content_max' => 'File size must not exceed 20MB.',
    'preview_required' => 'Preview status is required.',
    'preview_invalid' => 'Preview status is invalid.',
    'status_required' => 'Lecture status is required.',
    'status_invalid' => 'Lecture status is invalid.',
    'content_video_invalid' => 'Invalid video. Please upload a valid MP4 video file.',
    'content_file_invalid' => 'Invalid file. Please upload a valid PDF file.',
    'lecture_section_updated' => 'Lecture section updated successfully.',
    'section_not_found' => 'Section not found.',
    'lecture_status_updated' => 'Lecture status updated successfully.',
    'invalid_status' => 'Invalid status provided. Please provide either "active" or "inactive".',
    'invalid_data_format' => 'Invalid data format. Please provide a valid data array.',
    'order_update_success' => 'Order updated successfully.',
    'content_type_mismatch' => 'Invalid content type provided. Content type must be "lecture" or "quiz".',
    'lecture_not_found' => 'Lecture not found.',
    'quiz_not_found' => 'Quiz not found.',

    //QUIZ
    'quiz_not_found' => 'Quiz not found.',
    'quiz_fetch_success' => 'Quizzes fetched successfully.',
    'quiz_detail_success' => 'Quiz details retrieved successfully.',
    'quiz_create_success' => 'Quiz created successfully.',
    'quiz_update_success' => 'Quiz updated successfully.',
    'quiz_soft_delete_success' => 'Quiz soft deleted successfully.',
    'quiz_restore_success' => 'Quiz restored successfully.',
    'quiz_force_delete_success' => 'Quiz permanently deleted successfully.',

    //STUDY
    'course_not_purchased' => 'You have not purchased this course.',
    'course_access_granted' => 'You can access this course.',
    'no_courses_found' => 'No courses found.',
    'courses_retrieved_successfully' => 'Courses retrieved successfully.',

    // Question
    'quiz_id_required' => 'Quiz ID is required.',
    'quiz_id_invalid' => 'Quiz ID is invalid.',
    'question_required' => 'The question field is required.',
    'options_required' => 'Options are required.',
    'options_invalid_format' => 'Options must be a valid JSON array.',
    'answer_required' => 'Answer is required.',
    'status_required' => 'Status is required.',
    'status_invalid' => 'Status must be either active or inactive.',
    'question_not_found' => 'Question not found.',
    'question_create_success' => 'Question created successfully.',
    'question_update_success' => 'Question updated successfully.',
    'question_soft_delete_success' => 'Question soft deleted successfully.',
    'question_restore_success' => 'Question restored successfully.',
    'question_force_delete_success' => 'Question permanently deleted successfully.',
    'question_fetch_success' => 'Questions fetched successfully.',
    'question_detail_success' => 'Question details fetched successfully.',
    'validation_error' => 'Validation failed.',
    'question_not_found' => 'Question not found.',
    'quiz_not_found' => 'Quiz not found.',
    'invalid_status' => 'Invalid status. Allowed values are active or inactive.',
    'question_update_success' => 'Question updated successfully.',
    'questions_fetch_success' => 'Questions fetched successfully.',
    'questions_order_update_success' => 'Questions order updated successfully.',
    'invalid_data_format' => 'Invalid data format provided.',



    // CART
    'cart_items_fetched' => 'List of courses in cart retrieved successfully.',
    'course_not_found_in_cart' => 'Course not in cart.',
    'course_added_success' => 'Course added wishlist.',
    'course_already_in_cart' => 'Course already in cart.',
    'course_already_in_paid_order' => 'Course already in paid order.',
    'course_removed_success' => 'Course removed successfully.',
    'cart_cleared' => 'All courses have been removed from the cart.',

    // VOUCHER
    'voucher_created_success' => 'Voucher created successfully.',
    'voucher_code_already_exists' => 'Voucher code already exists.',
    'voucher_creation_failed' => 'Voucher creation failed.',
    'voucher_not_found' => 'Voucher not found.',
    'voucher_updated_success' => 'Voucher updated successfully.',
    'voucher_soft_deleted_success' => 'Voucher soft deleted successfully.',
    'voucher_restore_success' => 'Voucher restored successfully.',
    'voucher_has_expired' => 'Voucher has expired.',
    'voucher_usage_limit_reached' => 'Voucher usage limit reached.',
    'voucher_apply_success' => 'Voucher applied successfully.',

    // SECTIONS
    'sections_created_success' => 'sections created successfully.',
    'sections_code_already_exists' => 'sections code already exists.',
    'sections_creation_failed' => 'sections creation failed.',
    'sections_not_found' => 'sections not found.',
    'sections_updated_success' => 'sections updated successfully.',
    'sections_deleted_success' => 'sections deleted successfully.',
    'sections_restore_success' => 'sections restored successfully.',
    'sections_has_expired' => 'sections has expired.',
    'sections_usage_limit_reached' => 'sections usage limit reached.',
    'sections_apply_success' => 'sections applied successfully.',
    'section_not_found' => 'Section not found.',

    // ORDER
    'order_created_success' => 'Order created successfully..',
    'order_create_failed' => 'Failed to create order.',
    'order_require_email' => 'User email is required to create a checkout session',

    // Admin user
    //    'getUsers' => 'Lấy dữ liệu thành công',
    //    'updateUser' => 'Cập nhật thành công',
    'phone_number_update' => 'Phone number must be a number.',
    'address_update' => 'Address must be a string',
    'contactInfo_update' => 'Contact info must be array.',

    'user_has_blocked' => 'Account has been blocked.',
];
