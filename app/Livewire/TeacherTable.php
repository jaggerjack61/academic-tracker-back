<?php

namespace App\Livewire;

use App\Models\Teacher;
use Livewire\Component;

class TeacherTable extends Component
{
    public $search;

    public $paginate = 30;

    public function render()
    {
        if ($this->search) {
            $obj = new Teacher();
            $teachers = $obj->search($obj->query(), $this->search)->paginate($this->paginate);

            return view('livewire.teacher-table', compact('teachers'));
        } else {
            $teachers = Teacher::paginate($this->paginate);

            return view('livewire.teacher-table', compact('teachers'));

        }

    }
}
