<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Voucher;
use Faker\Factory as Faker;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Tạo 20 voucher ngẫu nhiên
        for ($i = 0; $i < 20; $i++) {
            Voucher::create([
                'voucher_code' => $faker->unique()->bothify('VC-####-???'), // Mã voucher duy nhất
                'description' => $faker->sentence(),
                'discount_type' => $faker->randomElement([1, 2]), // 1: Percentage, 2: Fixed amount
                'discount_value' => $faker->randomFloat(2, 5, 50), // Giá trị giảm giá từ 5 đến 50
                'min_order_value' => $faker->randomFloat(2, 20, 200), // Giá trị đơn hàng tối thiểu từ 20 đến 200
                'max_discount_value' => $faker->optional()->randomFloat(2, 50, 100), // Giá trị giảm tối đa
                'start_date' => $faker->date(),
                'end_date' => $faker->dateTimeBetween('+1 week', '+1 year')->format('Y-m-d'), // Ngày kết thúc từ một tuần đến một năm
                'status' => $faker->randomElement(['active', 'inactive']), // Trạng thái ngẫu nhiên
                'is_deleted' => 0, // Không bị xóa
            ]);
        }
    }
}
