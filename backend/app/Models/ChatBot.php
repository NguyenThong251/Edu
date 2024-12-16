<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatBot extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'conversation'];

    protected $casts = [
        'conversation' => 'array', // Tự động cast JSON thành mảng khi lấy ra
    ];
}

