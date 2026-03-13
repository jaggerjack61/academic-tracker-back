<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMember extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['group_id', 'profile_id', 'joined_at', 'is_active'];

    protected $casts = [
        'joined_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (ChatMember $member): void {
            if (! $member->joined_at) {
                $member->joined_at = now();
            }
        });
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ChatGroup::class, 'group_id');
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }
}
