<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Factories\Relationship;

class AbstractModel
{

    public function dob()
    {
        return "";
    }

    public function id_number()
    {
        return "";
    }

    public function phone_number()
    {
        return "";
    }

    public function is_active()
    {
        return true;
    }

    public function sex()
    {
        return "";
    }
}
