<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Voucher;
use App\Models\User;
use Faker\Factory as Faker;

class VoucherSeeder extends Seeder
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

        // Tạo 20 voucher ngẫu nhiên
        for ($i = 0; $i < 20; $i++) {
            // Tạo ngày kết thúc ngẫu nhiên
            $endDate = $faker->optional()->dateTimeBetween('+1 week', '+1 year');

            Voucher::create([
                'code' => $faker->unique()->bothify('VC-####-???'), // Mã voucher duy nhất
                'description' => $faker->sentence(),
                'discount_type' => $faker->randomElement([1, 2]), // 1: Percentage, 2: Fixed amount
                'discount_value' => $faker->randomFloat(2, 5, 50), // Giá trị giảm giá từ 5 đến 50
                'min_order_value' => $faker->optional()->randomFloat(2, 20, 200), // Giá trị đơn hàng tối thiểu từ 20 đến 200
                'max_discount_value' => $faker->optional()->randomFloat(2, 50, 100), // Giá trị giảm tối đa
                'start_date' => $faker->optional()->date(), // Ngày bắt đầu (có thể null)
                'end_date' => $endDate ? $endDate->format('Y-m-d') : null, // Kiểm tra và định dạng ngày kết thúc
                'remain_quantity' => $faker->optional()->numberBetween(1, 1000), // Số lượng còn lại
                'status' => $faker->randomElement(['active', 'inactive']), // Trạng thái ngẫu nhiên
                'deleted_by' => null, // Giá trị mặc định ban đầu
                'created_by' => $faker->randomElement($userIds), // Lấy một user id ngẫu nhiên từ danh sách user IDs
                'updated_by' => null, // Giá trị mặc định ban đầu
            ]);
        }
    }
}
