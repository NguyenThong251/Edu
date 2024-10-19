<?php

return [
    'welcome' => 'Welcome to our application!',
    'user_signup_success' => 'User signup successful.',
    'validation_error' => 'There were validation errors.',
    // Message for rolecheck Middleware
    'no_permission' => 'You do not have permission to perform this action.',
    // Message for JWT Middleware
    'token_invalid' => 'Invalid token',
    'token_expired' => 'Token has expired',
    'token_not_found' => 'Token not found',
    // Message for Validate Category
    'name_required' => 'Name is required.',
    'name_string' => 'Name must be a string.',
    'name_max' => 'Name may not be greater than :max characters.',
    'name_unique' => 'Name must be unique.',
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
];
