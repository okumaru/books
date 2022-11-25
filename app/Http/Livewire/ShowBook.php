<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ShowBook extends Component
{

    public $showForm = false;
    public $showView = false;

    public function render()
    {
        return view('livewire.show-book')
            ->layout('layouts.app', ['title' => 'List Books']);
    }
}
