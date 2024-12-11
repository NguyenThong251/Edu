<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $banners = Banner::query();

        if ($request->has('type')) {
            $banners->where('type', $request->type);
        }
        if ($request->has('position')) {
            $banners->where('position', $request->position);
        }
        return response()->json([
            'status' => 'SUCCESS',
            'data' => $banners->orderBy('priority', 'asc')->get(),
            'Get successful results'
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
            'position' => 'required|string',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|boolean',
            'priority' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'FAIL',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Upload ảnh lên S3
        $path = $request->file('image')->storePublicly('banners', 's3');
        $imageUrl = env('URL_IMAGE_S3') . $path;

        // Lưu banner vào database
        $banner = Banner::create(array_merge($validator->validated(), [
            'image_url' => $imageUrl,
        ]));

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $banner,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'type' => 'nullable|string',
            'position' => 'nullable|string',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'nullable|boolean',
            'priority' => 'nullable|integer|min:0',
        ]);
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu tồn tại
            $currentFilePath = str_replace(env('URL_IMAGE_S3'), '', $banner->image_url);
            if (Storage::disk('s3')->exists($currentFilePath)) {
                Storage::disk('s3')->delete($currentFilePath);
            }

            // Upload ảnh mới lên S3
            $path = $request->file('image')->storePublicly('banners', 's3');
            $banner->image_url = env('URL_IMAGE_S3') . $path;
        }

        // Cập nhật dữ liệu từ form
        $banner->update(array_merge($validator->validated(), [
            'image_url' => $banner->image_url,
        ]));

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $banner,
        ], 200);
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        // Xóa ảnh trên S3
        $currentFilePath = str_replace(env('URL_IMAGE_S3'), '', $banner->image_url);
        if (Storage::disk('s3')->exists($currentFilePath)) {
            Storage::disk('s3')->delete($currentFilePath);
        }

        // Xóa banner khỏi database
        $banner->delete();

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Banner deleted successfully.',
        ], 200);
    }
}
