<?php

namespace App\Livewire;

use App\Enums\ServiceStatus;
use App\Filament\Resources\ServiceResource\Pages\ViewService;
use App\Models\Service;
use Filament\Forms\Components\Radio;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class SetServiceStatusForm extends Component implements HasForms
{
    use InteractsWithForms;

    public $service;

    public function mount(Service $service): void{
        $this->service = $service->toArray();
        $this->form->fill($service->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Radio::make('status')->hiddenLabel(true)
                    ->inline()->inlineLabel(false)
                    ->options([
                        "AVAILABLE" => ServiceStatus::AVAILABLE->label(),
                        "PARTIAL" => ServiceStatus::PARTIAL->label(),
                        "OUTAGE" => ServiceStatus::OUTAGE->label(),
                        "MAINTENANCE" => ServiceStatus::MAINTENANCE->label(),
                    ])
                    ->default($this->service['status'])
                    ->columnSpan(2),


            ])
            ->statePath('service')
            ->model(Service::class)
            ->live();
    }

    public function updated($property){
        if($property == "service.status"){
            Service::where('id',$this->service['id'])->update(['status' => $this->service['status']]);
            //$this->dispatch("status_updated");
            $this->dispatch('$refresh', ViewService::class);
        }
    }

    public function render()
    {
        return view('livewire.set-service-status-form');
    }
}
