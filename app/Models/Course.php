<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

}
