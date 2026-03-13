<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'teacher_id',
        'activity_type_id',
        'course_id',
        'term_id',
        'note',
        'total',
        'due_date',
        'file',
        'is_active',
    ];

    protected $casts = [
        'total' => 'float',
        'is_active' => 'boolean',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'teacher_id');
    }

    public function activityType(): BelongsTo
    {
        return $this->belongsTo(ActivityType::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
