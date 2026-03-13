<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Relationship extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'parent_id', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'student_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'parent_id');
    }
}
