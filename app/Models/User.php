<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role(): belongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * @throws \Exception
     */
    public function userable($role = null)
    {
        return match ($this->role->name ?? $role) {
            'parent' => $this->belongsTo(StudentParent::class, 'id', 'user_id'),
            'student' => $this->belongsTo(Student::class, 'id', 'user_id'),
            'teacher' => $this->belongsTo(Teacher::class, 'id', 'user_id'),
            'admin' => $this->belongsTo(Admin::class, 'id', 'user_id'),
            default => throw new \Exception('Invalid role'),
        };
    }

    public function parent()
    {
        return $this->userable('parent');
    }

    public function student()
    {
        return $this->userable('student');
    }

    public function teacher()
    {
        return $this->userable('teacher');
    }

    public function admin()
    {
        return $this->userable('admin');
    }

    public function search($query, $search)
    {

        return $query->where('name', 'LIKE', "%{$search}%")
            ->orWhere('email', 'LIKE', "%{$search}%")
            ->orWhereHas('student', function ($q) use ($search) {
                $q->where('id_number', 'LIKE', "%{$search}%")
                    ->orWhere('phone_number', 'LIKE', "%{$search}%")
                    ->orWhere('dob', 'LIKE', "%{$search}%")
                    ->orWhere('sex', 'LIKE', "%{$search}%");
            })
            ->orWhereHas('parent', function ($q) use ($search) {
                $q->where('id_number', 'LIKE', "%{$search}%")
                    ->orWhere('phone_number', 'LIKE', "%{$search}%")
                    ->orWhere('dob', 'LIKE', "%{$search}%")
                    ->orWhere('sex', 'LIKE', "%{$search}%");
            })
            ->orWhereHas('teacher', function ($q) use ($search) {
                $q->where('id_number', 'LIKE', "%{$search}%")
                    ->orWhere('phone_number', 'LIKE', "%{$search}%")
                    ->orWhere('dob', 'LIKE', "%{$search}%")
                    ->orWhere('sex', 'LIKE', "%{$search}%");
            })
            ->orWhereHas('admin', function ($q) use ($search) {
                $q->where('id_number', 'LIKE', "%{$search}%")
                    ->orWhere('phone_number', 'LIKE', "%{$search}%")
                    ->orWhere('dob', 'LIKE', "%{$search}%")
                    ->orWhere('sex', 'LIKE', "%{$search}%");
            })
            ->orWhereHas('role', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
    }
}
