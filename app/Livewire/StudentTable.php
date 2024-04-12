<?php

namespace App\Livewire;

use App\Models\Student;
use Livewire\Component;

class StudentTable extends Component
{
    public $search;
    public $paginate = 30;

    public function render()
    {

        if($this->search){
            $obj = new Student();
            $students = $obj->search($obj->query(), $this->search)->paginate($this->paginate);
            return view('livewire.student-table', compact('students'));
        }
        else{
            $students = Student::paginate($this->paginate);
            return view('livewire.student-table', compact('students'));

        }




    }
}
