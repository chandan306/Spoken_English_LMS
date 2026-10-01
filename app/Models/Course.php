<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'course_name',
        'slug',
        'description',
        'price',
        'discount_price',
        'duration',
        'image',
        'status'
    ];

    /**
     * Keep links usable for legacy rows that do not yet have a slug.
     */
    public function getRouteKey(): mixed
    {
        return $this->slug ?: $this->getKey();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null): ?Model
    {
        $field ??= $this->getRouteKeyName();
        $course = $this->newQuery()->where($field, $value)->first();

        if (! $course && $field === 'slug' && ctype_digit((string) $value)) {
            $course = $this->newQuery()->find($value);
        }

        return $course;
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function getEffectivePriceAttribute(): string
    {
        return (string) ($this->discount_price ?? $this->price);
    }
}
