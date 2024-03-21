<?php

namespace App\Livewire;

use App\Enums\ServiceStatus;
use App\Models\Checks\CheckError;
use App\Models\Service;
use Carbon\Carbon;
use Faker\Provider\Text;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class ViewServiceDisplayLastErrors extends Component implements HasForms, HasTable
{
    use InteractsWithTable, InteractsWithForms;

    public $service;

    public function mount(Service $service){
        $this->service = $service;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(CheckError::query()->where('service_id',$this->service?->id)->orderBy('created_at','desc'))
            ->columns([
                TextColumn::make('created_at')
                    ->label('Survenue le')
                    ->date("d/m/Y H:i:s"),
                TextColumn::make('error_type')
                    ->label('Erreur'),
                TextColumn::make('error_message'),
                TextColumn::make('from_status')
                    ->label('Du statut...')
                    ->icon(fn (ServiceStatus $state): string => match ($state) {
                        ServiceStatus::AVAILABLE => 'far-face-smile',
                        ServiceStatus::PARTIAL => 'far-face-meh',
                        ServiceStatus::OUTAGE => 'far-face-dizzy',
                        ServiceStatus::MAINTENANCE => 'fas-hammer',
                    })->color(fn (ServiceStatus $state): string => match ($state) {
                        ServiceStatus::AVAILABLE => 'success',
                        ServiceStatus::PARTIAL => 'warning',
                        ServiceStatus::OUTAGE => 'danger',
                        ServiceStatus::MAINTENANCE => 'gray',
                    })
                    ->formatStateUsing(fn(ServiceStatus $state): string => $state->label()),
                TextColumn::make('to_status')
                    ->label('Au statut')
                    ->icon(fn (ServiceStatus $state): string => match ($state) {
                        ServiceStatus::AVAILABLE => 'far-face-smile',
                        ServiceStatus::PARTIAL => 'far-face-meh',
                        ServiceStatus::OUTAGE => 'far-face-dizzy',
                        ServiceStatus::MAINTENANCE => 'fas-hammer',
                    })->color(fn (ServiceStatus $state): string => match ($state) {
                        ServiceStatus::AVAILABLE => 'success',
                        ServiceStatus::PARTIAL => 'warning',
                        ServiceStatus::OUTAGE => 'danger',
                        ServiceStatus::MAINTENANCE => 'gray',
                    })
                    ->formatStateUsing(fn(ServiceStatus $state): string => $state->label()),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function render()
    {
        return view('livewire.view-service-display-last-errors');
    }
}
