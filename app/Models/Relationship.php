<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relationship extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];

    public function student()
    {
        return $this->hasOne(Profile::class, 'id', 'student_id');
    }
}
