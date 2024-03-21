<?php

namespace App\Filament\Resources;

use App\Enums\ServiceStatus;
use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Checks\CheckError;
use App\Models\Checks\DnsCheck;
use App\Models\Checks\HttpCheck;
use App\Models\Metric;
use App\Models\MetricPoint;
use App\Models\Service;
use App\Models\User;
use Filament\AvatarProviders\UiAvatarsProvider;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-server-stack';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Nom du service')->required(),
                Forms\Components\Textarea::make('description')->columnSpan(2),
                Forms\Components\Toggle::make('public')->label('Affiché sur la page de statut publique')->required(),
                Forms\Components\Toggle::make('show_availability')->label('Metrics affichées sur la page de statut publique')->required(),
                Forms\Components\Radio::make('status')->label('Statut - Surchargé par les checks si activés')
                    ->inline()->inlineLabel(false)
                    ->options([
                        "AVAILABLE" => ServiceStatus::AVAILABLE->label(),
                            "PARTIAL" => ServiceStatus::PARTIAL->label(),
                            "OUTAGE" => ServiceStatus::OUTAGE->label(),
                            "MAINTENANCE" => ServiceStatus::MAINTENANCE->label(),
                        ])->default('AVAILABLE')
                ->columnSpan(2),


            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nom du service')->description(fn (Service $service): string => $service->description, position: 'under'),
                IconColumn::make('status')
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
                    }),
                IconColumn::make('public')
                    ->icon(fn (bool $state): string => match ($state) {
                        true => 'fas-lock-open',
                        false => 'fas-lock',
                    })->color(fn (bool $state): string => match ($state) {
                        true => 'primary',
                        false => 'gray',
                    }),
                IconColumn::make('show_availability')
                    ->label('Metrics publiques')
                    ->icon(fn (bool $state): string => match ($state) {
                        true => 'fas-lock-open',
                        false => 'fas-lock',
                    })->color(fn (bool $state): string => match ($state) {
                        true => 'primary',
                        false => 'gray',
                    }),
                Tables\Columns\TextColumn::make('nb_checks')
                    ->label("Nb. de checks")
                    ->badge()
                    ->default(fn(Service $service) => $service->checks()->count()),
                Tables\Columns\TextColumn::make('metrics_count')
                    ->label("Nb. de metrics")
                    ->counts("metrics")
                    ->badge(),
                TextColumn::make('user.name')->label('Créé par')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->with('user'))
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->before(function (Service $record) {
                    HttpCheck::where('service_id',$record->id)->delete();
                    DnsCheck::where('service_id', $record->id)->delete();
                    CheckError::where('service_id', $record->id)->delete();
                    $record->metrics?->each( function($metric) {
                        MetricPoint::where('metric_id', $metric->id)->delete();
                        $metric->delete();
                    });
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->before(function (Service $record) {
                        HttpCheck::where('service_id',$record->id)->delete();
                        DnsCheck::where('service_id', $record->id)->delete();
                        CheckError::where('service_id', $record->id)->delete();
                        $record->metrics?->each( function($metric) {
                            MetricPoint::where('metric_id', $metric->id)->delete();
                            $metric->delete();
                        });
                    }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
            'view' => Pages\ViewService::route('/{record}/view')
        ];
    }
}
