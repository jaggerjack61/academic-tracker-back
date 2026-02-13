<?php

namespace App\Livewire;

use App\Models\Profile;
use Livewire\Component;

class TeacherTable extends Component
{
    public $search;

    public $paginate = 30;

    public function render()
    {
        if ($this->search) {
            $obj = new Profile();
            $teachers = $obj->search($obj->query(), $this->search)
                ->where('type', 'teacher')
                ->paginate($this->paginate);

            return view('livewire.teacher-table', compact('teachers'));
        } else {
            $teachers = Profile::where('type', 'teacher')->paginate($this->paginate);

            return view('livewire.teacher-table', compact('teachers'));

        }

    }
}
