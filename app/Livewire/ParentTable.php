<?php

namespace App\Livewire;

use App\Models\StudentParent;
use Livewire\Component;

class ParentTable extends Component
{
    public $search;

    public $paginate = 30;

    public function render()
    {
        if ($this->search) {
            $obj = new StudentParent();
            $parents = $obj->search($obj->query(), $this->search)->paginate($this->paginate);

            return view('livewire.parent-table', compact('parents'));
        } else {
            $parents = StudentParent::paginate($this->paginate);

            return view('livewire.parent-table', compact('parents'));

        }

    }
}
