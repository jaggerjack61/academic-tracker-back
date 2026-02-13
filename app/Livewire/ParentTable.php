<?php

namespace App\Livewire;

use App\Models\Profile;
use Livewire\Component;

class ParentTable extends Component
{
    public $search;

    public $paginate = 30;

    public function render()
    {
        if ($this->search) {
            $obj = new Profile();
            $parents = $obj->search($obj->query(), $this->search)
                ->where('type', 'parent')
                ->paginate($this->paginate);

            return view('livewire.parent-table', compact('parents'));
        } else {
            $parents = Profile::where('type', 'parent')->paginate($this->paginate);

            return view('livewire.parent-table', compact('parents'));

        }

    }
}
