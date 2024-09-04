<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $guarded = [];
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)->withPivot('points');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getEnrolledCoursesCountAttribute(): int
    {
        return $this->courses()
            ->where(function ($query) {
                $query->where('status', 'ongoing')
                      ->orWhere('status', 'waiting_to_start');
            })
            ->count();
    }

    public function getCompletedCoursesCountAttribute(): int
    {
        return $this->courses()->where('status', 'done')->count();
    }
}
