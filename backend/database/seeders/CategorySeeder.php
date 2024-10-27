<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Category;
use App\Models\User;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        $userIds = User::pluck('id')->toArray();
        // Giả sử chúng ta tạo 15 category
        for ($i = 0; $i < 15; $i++) {
            Category::create([
                'name' => $faker->unique()->word, // Tên danh mục duy nhất
                'image' => 'https://picsum.photos/640/480?random=' . $faker->unique()->numberBetween(1, 1000),
                'description' => $faker->sentence(), // Mô tả ngẫu nhiên
                'status' => $faker->randomElement(['active', 'inactive']), // Trạng thái ngẫu nhiên
                'parent_id' => $faker->optional()->numberBetween(1, 15), // Ngẫu nhiên chọn parent_id hoặc null
                'deleted_by' => null, // Giá trị mặc định là null
                'created_by' => $faker->randomElement($userIds), // Chọn ngẫu nhiên ID từ danh sách user
                'updated_by' => $faker->optional()->randomElement($userIds), // Ngẫu nhiên người cập nhật hoặc null
            ]);
        }
    }
}
