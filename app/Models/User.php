<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'user_id', 'id');
    }

    public function userable($type = null)
    {
        return $this->profile()->when($type, fn ($q) => $q->where('type', $type));
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

    public function parent()
    {
        return $this->userable('parent');
    }

    public function search($query, $search)
    {

        return $query->where('name', 'LIKE', "%{$search}%")
            ->orWhere('email', 'LIKE', "%{$search}%")
            ->orWhereHas('profile', function ($q) use ($search) {
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
