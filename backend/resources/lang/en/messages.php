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

    'error_save' => 'Save failed.',
    'successful_save' => 'Save successfully.',
    'update_fail' => 'Update failed.',
    'update_success' => 'Update successfully.',

];
