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
        $validator = Validator::make(request()->all(), [
            'name' => 'required|max:100',
            'status' => 'required|in:active,inactive',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return formatResponse(422, STATUS_FAIL, '', $validator->errors(), 'Validation failed');
        }
        $category = Category::create($validatedData);

        return response()->json($category, 201);
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
        $category = Category::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|max:100',
            'description' => 'required',
            'status' => 'required|in:active,inactive',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category->update($validatedData);

        return response()->json($category);
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
