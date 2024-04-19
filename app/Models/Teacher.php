<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    protected $guarded = ['created_at', 'updated_at'];

    public function getNameAttribute():string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function courses()
    {
        return $this->hasMany(CourseTeacher::class, 'teacher_id', 'id')->where('is_active', true);
    }

    public function search($query, $search)
    {
        return $query->where('first_name', 'LIKE' , '%'.$search.'%')
            ->orWhere('last_name', 'LIKE' , '%'.$search.'%')
            ->orWhere('phone_number', 'LIKE' , '%'.$search.'%')
            ->orWhere('id_number', 'LIKE' , '%'.$search.'%')
            ->orWhere('dob', 'LIKE' , '%'.$search.'%')
            ->orWhere('sex', 'LIKE' , '%'.$search.'%');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
