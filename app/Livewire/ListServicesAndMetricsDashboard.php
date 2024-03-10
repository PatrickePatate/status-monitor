<?php

namespace App\Livewire;

use App\Enums\ServiceStatus;
use App\Models\Service;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ListServicesAndMetricsDashboard extends Component implements HasForms, HasTable
{
    use InteractsWithTable, InteractsWithForms;

    protected $listeners = ['refresh'];

    public function render()
    {
        return view('livewire.list-services-and-metrics-dashboard');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Service::with('metrics'))
            ->columns([
                TextColumn::make('name')
                ->label('Service'),
                TextColumn::make('metrics.name'),
                TextColumn::make('status')
                    ->label('Statut')
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

    public function refresh(){
        $this->render();
    }
}
