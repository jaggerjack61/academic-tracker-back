<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];

    public function student(): belongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): belongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
