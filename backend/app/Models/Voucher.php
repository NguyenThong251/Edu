<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Các thuộc tính có thể được gán hàng loạt.
     *
     * @var array<int, string>
     */
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'usage_limit',
        'usage_count',
        'expires_at',
        'min_order_value',
        'max_discount_value',
        'status',
        'created_by',
        'deleted_by',
        'updated_by'
    ];

    // Kiểm tra voucher có hợp lệ không
    public function isValid($orderTotal)
    {
        return $this->status === 'active' &&
            $this->expires_at >= Carbon::now() &&
            ($this->usage_limit === null || $this->usage_count < $this->usage_limit) &&
            ($this->min_order_value === null || $orderTotal >= $this->min_order_value);
    }


    // Áp dụng giảm giá và trả về số tiền giảm giá (giới hạn trong max_discount_value nếu có)
    public function applyDiscount($orderTotal)
    {
        $discount = $this->discount_type === 'percent'
            ? $orderTotal * $this->discount_value / 100
            : $this->discount_value;

        return min($discount, $this->max_discount_value ?? $discount);
    }

    /**
     * Các thuộc tính sẽ được coi là kiểu ngày tháng.
     *
     * @var array<string, string>
     */
    protected $dates = ['start_date', 'end_date', 'deleted_at'];

    /**
     * Định nghĩa mối quan hệ với người dùng (User) đã tạo.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Định nghĩa mối quan hệ với người dùng (User) đã cập nhật.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Định nghĩa mối quan hệ với người dùng (User) đã xóa.
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
