<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];

    public function logs()
    {
        return $this->hasMany(ActivityLog::class, 'activity_id', 'id');
    }

    public function type()
    {
        return $this->hasOne(ActivityType::class, 'id', 'activity_type_id');
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'id', 'teacher_id');
    }

    public function course()
    {
        return $this->hasOne(Course::class, 'id', 'course_id');
    }
}
