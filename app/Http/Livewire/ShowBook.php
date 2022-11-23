<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ShowBook extends Component
{
    public function render()
    {
        return view('livewire.show-book')
            ->layout('layouts.app', ['title' => 'List Books']);
    }
}
