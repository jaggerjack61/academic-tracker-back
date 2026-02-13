<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Course extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];

    public function grade(): hasOne
    {
        return $this->hasOne(Grade::class, 'id', 'grade_id');
    }

    public function subject(): hasOne
    {
        return $this->hasOne(Subject::class, 'id', 'subject_id');
    }

    public function teacher()
    {
        return $this->hasOne(CourseTeacher::class, 'course_id', 'id')->where('is_active', true);
    }

    public function students()
    {
        return $this->hasMany(CourseStudent::class, 'course_id', 'id')->where('is_active', true);
    }

    public function search($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%")
            ->orWhereHas('teacher', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhereHas('teacher', function ($query) use ($search) {
                        $query->where('first_name', 'LIKE', "%{$search}%")->orWhere('last_name', 'LIKE', "%{$search}%");
                    });
            })
            ->orWhereHas('subject', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            })
            ->orWhereHas('grade', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'course_id', 'id');
    }
}
