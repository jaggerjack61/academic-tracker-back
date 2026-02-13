<?php

namespace App\Livewire;

use App\Models\Course;
use Livewire\Component;

class ClassTable extends Component
{
    public $search;

    public $paginate = 30;

    public function render()
    {

        if ($this->search) {
            $course = new Course;
            $classes = $course->search($course->query(), $this->search)->paginate($this->paginate);

            return view('livewire.class-table', compact('classes'));
        } else {
            $classes = Course::paginate($this->paginate);

            return view('livewire.class-table', compact('classes'));

        }

    }
}
