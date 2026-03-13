<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'description', 'grade_id', 'subject_id', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(CourseTeacher::class);
    }

    public function studentEnrollments(): HasMany
    {
        return $this->hasMany(CourseStudent::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function chatGroup(): HasOne
    {
        return $this->hasOne(ChatGroup::class);
    }
}
