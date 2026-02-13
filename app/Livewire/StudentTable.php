<?php

namespace App\Livewire;

use App\Models\Profile;
use Livewire\Component;

class StudentTable extends Component
{
    public $search;

    public $paginate = 30;

    public function render()
    {

        if ($this->search) {
            $obj = new Profile();
            $students = $obj->search($obj->query(), $this->search)
                ->where('type', 'student')
                ->paginate($this->paginate);

            return view('livewire.student-table', compact('students'));
        } else {
            $students = Profile::where('type', 'student')->paginate($this->paginate);

            return view('livewire.student-table', compact('students'));

        }

    }
}
