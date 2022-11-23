<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\ShowBook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class ShowBookTest extends TestCase
{
    /** @test */
    public function the_component_can_render()
    {
        $component = Livewire::test(ShowBook::class);

        $component->assertStatus(200);
    }
}
