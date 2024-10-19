<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'user_id',
        'level_id',
        'title',
        'short_description',
        'description',
        'thumbnail',
        'price',
        'type_sale',
        'sale_value',
        'status',
    ];

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }


    public function averageRating()
    {
        return $this->reviews()->avg('rating');
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Review::class, 'course_id');
    }

    public function sections(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Section::class, 'course_id');
    }
    public function getTotalDurationAttribute()
    {
        return $this->sections->sum(function ($section) {
            return $section->lectures->sum('duration');
        });
    }



}
