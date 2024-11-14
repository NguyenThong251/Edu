<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lecture;
use App\Models\User;
use App\Models\ProgressLecture;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ProgressLectureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ProgressLecture::create([
        //     'user_id' => 1,
        //     'lecture_id' => 1,
        //     'learned' => 1,
        //     'percent' => 1,
        //     'status' => 'active',
        //     'created_by' => 1,
        //     'updated_by' =>1,
        //     'deleted_by' => 1,
        //     // 'created_at' => now(),
        //     // 'updated_at' => now(),
        //     // 'deleted_at' => null,
        // ]);
        $faker = Faker::create();

        // Lấy tất cả lectures
        $lectures = Lecture::select('id', 'duration')->get();

        // Nếu không có dữ liệu trong lectures, hiển thị thông báo
        if ($lectures->isEmpty()) {
            $this->command->info('Không có dữ liệu trong bảng lectures.');
            return;
        }

        // Lấy tất cả ID từ bảng users
        $userIds = User::pluck('id')->toArray();

        // Tạo dữ liệu progress cho mỗi lecture và user
        foreach ($lectures as $lecture) {
            // Tạo progress cho một số người dùng ngẫu nhiên
            $assignedUsers = $faker->randomElements($userIds, $faker->numberBetween(1, count($userIds)));

            foreach ($assignedUsers as $userId) {
                // Random percent: 0, 50, 100
                $percent = $faker->randomElement([0, 50, 100]);

                // Tính learned dựa trên percent
                $learned = $lecture->duration * ($percent / 100);

                ProgressLecture::create([
                    'user_id' => $userId,
                    'lecture_id' => $lecture->id,
                    'learned' => $learned,
                    'percent' => $percent,
                    'status' => $faker->randomElement(['active']),
                    'created_by' => $faker->randomElement($userIds),
                    'updated_by' => $faker->optional()->randomElement($userIds),
                    'deleted_by' => null,
                    // 'created_at' => now(),
                    // 'updated_at' => now(),
                    // 'deleted_at' => null,
                ]);
            }
        }

        $this->command->info('ProgressLectureSeeder: Dữ liệu seed thành công.');
    }
}
