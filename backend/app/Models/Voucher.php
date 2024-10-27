<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Các thuộc tính có thể được gán hàng loạt.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'max_discount',
        'min_order_value',
        'start_date',
        'end_date',
        'usage_remain',
        'deleted_by',
        'created_by',
        'updated_by',
    ];

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
