<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Enums\ServiceStatus;
use App\Filament\Resources\ServiceResource;
use App\Models\Service;
use Filament\Actions;
use Filament\Forms\Components\Radio;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;

class ViewService extends ViewRecord
{
    protected static string $resource = ServiceResource::class;

    protected static string $view = 'filament.pages.services.view-service';

    protected $listeners = [ '$refresh' => 'refresh'];

    /**
     * @return string|\Illuminate\Contracts\Support\Htmlable
     */
    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return $this->data['name'];
    }

    public function refresh(){
        $this->data = Service::find($this->data['id'])->toArray();
        $this->render();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $data = $this->getViewData();
        $data['data'] = $this->data;
        return view($this->getView(), $data )
            ->layout($this->getLayout(), [
                'livewire' => $this,
                'maxContentWidth' => $this->getMaxContentWidth(),
                ...$this->getLayoutData(),
            ]);
    }


}
