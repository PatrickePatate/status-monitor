<?php

namespace App\Livewire;

use App\Models\Service;
use Livewire\Component;

class ViewServiceDisplayMetrics extends Component
{
    public $service;

    public function mount(Service $service){
        $this->service = $service;
    }
    public function render()
    {
        return view('livewire.view-service-display-metrics');
    }
}
