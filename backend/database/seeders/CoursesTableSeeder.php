<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;


class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $totalCategories = Category::all()->count();
        for ($i = 0; $i < 10; $i++) {
            Course::create([
                'category_id' => $faker->numberBetween(1, $totalCategories),
                'title' => $faker->sentence(3),
                'description' => $faker->paragraph,
                'thumbnail' => $faker->imageUrl($width = 640, $height = 480, 'courses'),
                'price' => $faker->randomFloat(2, 100, 1000),
                'type_sale' => $faker->randomElement(['percent', 'price']),
                'sale_value' => $faker->randomFloat(2, 0, 100),
                'is_deleted' => $faker->boolean,
                'status' => $faker->randomElement(['active', 'inactive']),
            ]);
        }
    }
}
