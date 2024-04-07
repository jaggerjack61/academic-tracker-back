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

}
