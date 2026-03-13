<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'first_name',
        'last_name',
        'dob',
        'sex',
        'phone_number',
        'is_active',
        'id_number',
        'user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(CourseTeacher::class, 'teacher_id');
    }

    public function courseEnrollments(): HasMany
    {
        return $this->hasMany(CourseStudent::class, 'student_id');
    }

    public function parentRelationships(): HasMany
    {
        return $this->hasMany(Relationship::class, 'student_id');
    }

    public function studentRelationships(): HasMany
    {
        return $this->hasMany(Relationship::class, 'parent_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'teacher_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'student_id');
    }

    public function specialFees(): HasMany
    {
        return $this->hasMany(SpecialFee::class, 'student_id');
    }

    public function paymentPlans(): HasMany
    {
        return $this->hasMany(PaymentPlan::class, 'student_id');
    }

    public function feePayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'student_id');
    }

    public function createdGroups(): HasMany
    {
        return $this->hasMany(ChatGroup::class, 'created_by_profile_id');
    }

    public function chatMemberships(): HasMany
    {
        return $this->hasMany(ChatMember::class, 'profile_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'sender_profile_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
