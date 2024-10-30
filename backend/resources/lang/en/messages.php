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

    // CART: handle courses in cart
    'cart_items_fetched' => 'List of courses in cart retrieved successfully.',
    'course_not_found_in_cart' => 'Course not in cart.',
    'course_added_success' => 'Course added successfully.',
    'course_already_in_cart' => 'Course already in cart.',
    'course_already_in_paid_order' => 'Course already in paid order.',
    'course_removed_success' => 'Course removed successfully.',
    'cart_cleared' => 'All courses have been removed from the cart.',

    // ORDER: handle orders
    'order_created_success' => 'Order created successfully.',
    'order_create_failed' => 'Failed to create order',
];
