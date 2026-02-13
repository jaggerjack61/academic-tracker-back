<?php

namespace App\Livewire;

use App\Models\Role;
use App\Models\User;
use Livewire\Component;

class UserTable extends Component
{
    public $search;

    public $paginate = 30;

    public function render()
    {
        if ($this->search) {
            $roles = Role::all();
            $obj = new User();
            $users = $obj->search($obj->query(), $this->search)->paginate($this->paginate);

            return view('livewire.user-table', compact('users', 'roles'));
        } else {
            $roles = Role::all();
            $users = User::paginate($this->paginate);

            return view('livewire.user-table', compact('users', 'roles'));

        }
    }
}
