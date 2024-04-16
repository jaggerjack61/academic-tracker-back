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
}
