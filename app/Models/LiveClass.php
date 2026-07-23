<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveClass extends Model
{
     protected $fillable = [
        'title',
        'course_id',
        'teacher_name',
        'class_date',
        'start_time',
        'end_time',
        'meeting_link',
        'meeting_id',
        'meeting_password',
        'status'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
