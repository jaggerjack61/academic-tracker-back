<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseTeacher extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'id', 'teacher_id');
    }

    public function course()
    {
        return $this->hasOne(Course::class, 'id', 'course_id');
    }

    public function teacherName()
    {
        return $this->teacher->name;
    }

    public function teacherID()
    {
        return $this->teacher->id;
    }

    public function getNameAttribute()
    {
        return $this->teacher->name;
    }
}

