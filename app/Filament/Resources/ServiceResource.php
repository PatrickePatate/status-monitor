<?php

namespace App\Filament\Resources;

use App\Enums\ServiceStatus;
use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use App\Models\User;
use Filament\AvatarProviders\UiAvatarsProvider;
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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Nom du service'),
                Forms\Components\Textarea::make('description')->columnSpan(2),
                Forms\Components\Toggle::make('public')->label('Affiché sur la page de statut publique'),
                Forms\Components\Toggle::make('show_availability')->label('Metrics affichées sur la page de statut publique'),
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
                ImageColumn::make('user.avatar')->label('Créé par')->circular()->default(fn(User $user) => $user->getFilamentAvatarUrl()),
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

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
        ];
    }
}
