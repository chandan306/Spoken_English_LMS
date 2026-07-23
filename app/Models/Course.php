<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'course_name',
        'description',
        'price',
        'duration',
        'image',
        'status'
    ];
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
