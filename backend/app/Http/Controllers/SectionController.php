<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
        ]);

        // check duplicate
        if (Section::where('course_id', $request->course_id)->where('name', $request->name)->exists()) {
            return formatResponse(STATUS_FAIL, '', '', __('messages.sections_code_already_exists'));
        }

        // insert section
        $section = new Section();
        $section->name = $request->name;
        // $section->user_id   = auth()->user()->id;
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
            'name' => 'required',
        ]);

        
        // Kiểm tra xem tên mới có bị trùng với tên khác trong bảng 'sections' không
        $section = Section::find($id); // Tìm section cần cập nhật
        if ($section) {
            // Kiểm tra trùng lặp, ngoại trừ chính section đang sửa
            if (Section::where('name', $request->name)->where('id', '!=', $id)->exists()) {
                return formatResponse(STATUS_FAIL, '', '', __('messages.sections_code_already_exists'));
            }

            // Cập nhật dữ liệu
            $section->name = $request->input('name');
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

    // public function sort(Request $request)
    // {
    //     $sections = json_decode($request->itemJSON);
    //     foreach ($sections as $key => $value) {
    //         $updater = $key + 1;
    //         Section::where('id', $value)->update(['sort' => $updater]);
    //     }
    // }
}

