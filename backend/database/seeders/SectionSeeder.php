<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Section;
use App\Models\User;
use Faker\Factory as Faker;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Lấy tất cả ID từ bảng courses
        $courseIds = Course::pluck('id')->toArray();

        // Nếu không có dữ liệu trong courses, hiển thị thông báo
        if (empty($courseIds)) {
            $this->command->info('Không có dữ liệu trong bảng courses.');
            return;
        }
        $userIds = User::pluck('id')->toArray();
        // Tạo 100 section cho các khóa học
        for ($i = 0; $i < 100; $i++) {
            Section::create([
                'course_id' => $faker->randomElement($courseIds), // Chọn ngẫu nhiên course_id từ mảng courseIds
                'name' => $faker->sentence(2),                    // Tên section với 2 từ
                'status' => $faker->randomElement(['active', 'inactive']), // Trạng thái ngẫu nhiên
                'deleted_by' => null,                              // Giá trị mặc định là null
                'created_by' => $faker->optional()->randomElement($userIds), // Chọn ngẫu nhiên ID từ danh sách user
                'updated_by' => $faker->optional()->randomElement($userIds), // Người cập nhật ngẫu nhiên hoặc null
            ]);
        }
    }
}
