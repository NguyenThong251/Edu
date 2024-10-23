<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Giả sử chúng ta tạo 15 category
        for ($i = 0; $i < 15; $i++) {
            Category::create([
                'name' => $faker->unique()->word, // Tên danh mục duy nhất
                'image' => $faker->imageUrl(640, 480, 'category'), // Hình ảnh ngẫu nhiên cho category
                'description' => $faker->sentence(), // Mô tả ngẫu nhiên
                'status' => $faker->randomElement(['active', 'inactive']), // Trạng thái ngẫu nhiên
                'parent_id' => $faker->optional()->numberBetween(1, 15), // Ngẫu nhiên chọn parent_id hoặc null
                'deleted_by' => null, // Giá trị mặc định là null
                'is_deleted' => 0, // Không bị xóa
                'created_by' => $faker->optional()->numberBetween(1, 10), // Ngẫu nhiên người tạo hoặc null
                'updated_by' => $faker->optional()->numberBetween(1, 10), // Ngẫu nhiên người cập nhật hoặc null
            ]);
        }
    }
}
