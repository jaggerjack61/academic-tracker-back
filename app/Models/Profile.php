<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getNameAttribute(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function courses()
    {
        return match ($this->type) {
            'teacher' => $this->hasMany(CourseTeacher::class, 'teacher_id', 'id')->where('is_active', true),
            'student' => $this->hasMany(CourseStudent::class, 'student_id', 'id')->where('is_active', true),
            default => $this->hasMany(CourseStudent::class, 'student_id', 'id')->whereRaw('1 = 0'),
        };
    }

    public function relationships()
    {
        return $this->hasMany(Relationship::class, 'parent_id', 'id')->where('is_active', true);
    }

    public function activities()
    {
        return $this->hasMany(ActivityLog::class, 'student_id', 'id');
    }

    public function search($query, $search)
    {
        return $query->where('first_name', 'LIKE', '%'.$search.'%')
            ->orWhere('last_name', 'LIKE', '%'.$search.'%')
            ->orWhere('phone_number', 'LIKE', '%'.$search.'%')
            ->orWhere('id_number', 'LIKE', '%'.$search.'%')
            ->orWhere('dob', 'LIKE', '%'.$search.'%')
            ->orWhere('sex', 'LIKE', '%'.$search.'%');
    }
}
