<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class LandingPage extends Component
{
    #[Layout('components.layouts.app')]
    #[Title('LaundryYuk - Layanan Laundry Delivery Terpadu')]
    public function render()
    {
        return view('livewire.landing-page');
    }
}