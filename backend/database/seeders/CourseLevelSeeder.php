<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CourseLevel;
use App\Models\User; // Import User model
use Faker\Factory as Faker;

class CourseLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Lấy tất cả user IDs hiện có từ bảng users
        $userIds = User::pluck('id')->toArray();

        // Nếu không có user ID nào, dừng việc seed
        if (empty($userIds)) {
            $this->command->info('No users found in the users table. Please seed the users table first.');
            return;
        }

        // Tạo 10 bản ghi ngẫu nhiên cho bảng course_levels
        for ($i = 0; $i < 3; $i++) {
            CourseLevel::create([
                'name' => $faker->unique()->word(), // Tên cấp độ ngẫu nhiên và duy nhất
                'status' => $faker->randomElement(['active', 'inactive']), // Trạng thái ngẫu nhiên
                'deleted_by' => null, // Không có giá trị ban đầu
                'created_by' => $faker->randomElement($userIds), // Lấy một user id ngẫu nhiên từ danh sách user IDs
                'updated_by' => null, // Không có giá trị ban đầu
            ]);
        }
    }
}
