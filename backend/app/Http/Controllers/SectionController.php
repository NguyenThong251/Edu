<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{

    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required',
        ]);

        // check duplicate
        if (Section::where('course_id', $request->course_id)
        ->where('title', $request->title)
        ->exists()) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.sections_code_already_exists'));
        }

        // insert section
        $section = new Section();
        $section->title = $request->title;
        $section->course_id = $request->course_id;
        $done = $section->save();

        return formatResponse(STATUS_OK, $section, '', __('messages.sections_created_success'));

    }
    public function showOne($id)
    {
        $section = Section::where('id', $id)->first();
        if (!$section) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.section_not_found'));
        }
        return formatResponse(STATUS_OK, $section, '', __('messages.course_found'));
    }

    public function update(Request $request, $id)
    {
        // Validate incoming request
        $request->validate([
            'title' => 'required',
        ]);

        
        // Kiểm tra xem tên mới có bị trùng với tên khác trong bảng 'sections' không
        $section = Section::find($id); // Tìm section cần cập nhật
        if ($section) {
            // Kiểm tra trùng lặp, ngoại trừ chính section đang sửa
            if (Section::where('title', $request->title)->where('id', '!=', $id)->exists()) {
                return formatResponse(STATUS_FAIL, '', '', __('messages.sections_code_already_exists'));
            }

            // Cập nhật dữ liệu
            $section->title = $request->input('title');
            $section->save();

            // Trả về phản hồi thành công
            return formatResponse(STATUS_OK, '', __('messages.sections_updated_success'));
        }

        // Nếu không tìm thấy section với ID
        return formatResponse(STATUS_FAIL, '', '', __('messages.section_not_found'));
    }


    public function delete($id)
    {
        Section::where('id', $id)->delete();
        return formatResponse(STATUS_OK, '', '', __('messages.sections_deleted_success'));
    }

    public function sort(Request $request)
    {
        $sortedSections = $request->input('sortedSections');
        // Ghi log dữ liệu sortedSections
        Log::info('Dữ liệu đã sắp xếp nè aaaa:', ['sortedSections' => $sortedSections]);

        // Kiểm tra nếu dữ liệu có hợp lệ
        if (empty($sortedSections)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không có dữ liệu để sắp xếp!'
            ]);
        }

        foreach ($sortedSections as $index => $section) {
            Section::where('id', $section['id'])->update(['order' => $index + 1]);
        }

        return response()->json([
            'status' => 'OK',
            'message' => 'Dữ liệu đã được sắp xếp thành công'
        ]);
    }
}

