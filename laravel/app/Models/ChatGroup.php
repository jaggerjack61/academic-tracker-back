<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'is_class_group', 'course_id', 'created_by_profile_id'];

    protected $casts = [
        'is_class_group' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'created_by_profile_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ChatMember::class, 'group_id');
    }

    public function activeMembers(): HasMany
    {
        return $this->hasMany(ChatMember::class, 'group_id')->where('is_active', true);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'group_id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class, 'group_id')->latestOfMany('created_at');
    }
}
