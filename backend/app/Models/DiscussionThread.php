<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'lecture_id',
        'user_id',
        'parent_id',
        'type',
        'title',
        'content',
    ];

    public function course()
    {
        return $this->belongsTo(Lecture::class);
    }

    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(DiscussionThread::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(DiscussionThread::class, 'parent_id');
    }

    public function likedBy()
    {
        return $this->belongsToMany(User::class, 'discussion_likes', 'discussion_thread_id',
            'user_id');
    }
}
