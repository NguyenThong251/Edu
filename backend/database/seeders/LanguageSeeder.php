<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\User;
use Faker\Factory as Faker;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Lấy tất cả user IDs hiện có
        $userIds = User::pluck('id')->toArray();

        // Tạo 10 bản ghi ngẫu nhiên cho bảng languages
        for ($i = 0; $i < 3; $i++) {
            Language::create([
                'name' => $faker->unique()->word(), // Tên ngôn ngữ ngẫu nhiên và duy nhất
                'description' => $faker->sentence(), // Mô tả ngôn ngữ ngẫu nhiên
                'status' => $faker->randomElement(['active', 'inactive']), // Trạng thái ngẫu nhiên
                'deleted_by' => null, // Không có giá trị ban đầu
                'created_by' => $faker->randomElement($userIds), // Lấy một user id ngẫu nhiên từ danh sách user IDs
                'updated_by' => null, // Không có giá trị ban đầu
            ]);
        }
    }
}
