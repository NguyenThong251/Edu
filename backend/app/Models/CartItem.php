<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['cart_id', 'course_id', 'price'];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function getFormattedPriceAttribute()
    {
        return $this->course->type_sale === 'percent'
            ? round($this->course->price - ($this->course->price * $this->course->sale_value / 100))
            : round($this->course->price - $this->course->sale_value);
    }
}
