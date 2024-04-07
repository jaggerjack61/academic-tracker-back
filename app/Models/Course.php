<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    use HasFactory;
    protected $guarded = ['created_at', 'updated_at'];

    public function grade():belongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function subject():belongsTo
    {
        return $this->belongsTo(Subject::class);
    }

}
