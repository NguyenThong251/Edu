<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;


class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('children')->get();
        return response()->json($categories);
    }

    // Tạo mới category
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:categories',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'parent_id' => 'nullable|exists:categories,id',
        ], [
            'name.required' => __('messages.name_required'),
            'name.string' => __('messages.name_string'),
            'name.max' => __('messages.name_max'),
            'name.unique' => __('messages.name_unique'),
            'description.string' => __('messages.description_string'),
            'status.required' => __('messages.status_required'),
            'status.in' => __('messages.status_invalid'),
            'parent_id.exists' => __('messages.parent_id_invalid'),
        ]);

        if ($validator->fails()) {
            return formatResponse(STATUS_FAIL, '', $validator->errors(), __('messages.validation_error'));
        }

        $category = new Category();
        $category->name = $request->name;
        $category->description = $request->description;
        $category->status = $request->status;
        $category->parent_id = $request->parent_id;
        $category->created_by = auth()->id();
        $category->save();

        return formatResponse(STATUS_OK, $category, '', __('messages.category_create_success'));
    }

    // Hiển thị một category cụ thể
    public function show($id)
    {
        $category = Category::with('children')->findOrFail($id);
        return response()->json($category);
    }

    // Cập nhật category
    public function update(Request $request, $id)
    {
        // Kiểm tra xem category có tồn tại hay không
        $category = Category::find($id);
        if (!$category) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.category_not_found'));
        }

        // Validation rules cho việc update
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories')->ignore($category->id) // Bỏ qua unique cho chính category hiện tại
            ],
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'parent_id' => 'nullable|exists:categories,id',
        ], [
            'name.required' => __('messages.name_required'),
            'name.string' => __('messages.name_string'),
            'name.max' => __('messages.name_max'),
            'name.unique' => __('messages.name_unique'),
            'description.string' => __('messages.description_string'),
            'status.required' => __('messages.status_required'),
            'status.in' => __('messages.status_invalid'),
            'parent_id.exists' => __('messages.parent_id_invalid'),
        ]);

        // Kiểm tra validation
        if ($validator->fails()) {
            return formatResponse(STATUS_FAIL, '', $validator->errors(), __('messages.validation_error'));
        }

        // Cập nhật thông tin category
        $category->name = $request->name;
        $category->description = $request->description;
        $category->status = $request->status;
        $category->parent_id = $request->parent_id;
        $category->updated_by = auth()->id(); // Thêm thông tin người cập nhật
        $category->save();

        return formatResponse(STATUS_OK, $category, '', __('messages.category_update_success'));
    }

    // Xóa category (soft delete)
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete(); // soft delete

        return response()->json(['message' => 'Category deleted']);
    }

    // Khôi phục category bị soft deleted
    public function restore($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();

        return response()->json(['message' => 'Category restored']);
    }
}
