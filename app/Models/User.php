<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Helpers\AbstractModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
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

    public function role():belongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * @throws \Exception
     */
    public function userable()
    {
        return match ($this->role->name) {
            'parent' => $this->belongsTo(StudentParent::class, 'user_id', 'id'),
            'student' => $this->belongsTo(Student::class, 'id', 'user_id'),
            'teacher' => $this->belongsTo(Teacher::class, 'id', 'user_id'),
            'admin' => $this->belongsTo(Admin::class, 'id', 'user_id'),
            default => throw new \Exception('Invalid role'),
        };
    }
}
