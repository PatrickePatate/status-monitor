<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MetricResource\Pages;
use App\Filament\Resources\MetricResource\RelationManagers;
use App\Models\Metric;
use App\Models\MetricPoint;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MetricResource extends Resource
{
    protected static ?string $model = Metric::class;
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-s-chart-bar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom de la metric')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('service_id')
                    ->relationship(name: "service", titleAttribute: "name")
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('suffix')
                    ->maxLength(255),
                Forms\Components\TextInput::make('warning_under')
                    ->label('Dégradé en-dessous de')
                    ->placeholder("Laisser vide pour ignorer")
                    ->nullable()
                    ->numeric(),
                Forms\Components\TextInput::make('danger_under')
                    ->label('En panne en-dessous de')
                    ->placeholder("Laisser vide pour ignorer")
                    ->nullable()
                    ->numeric(),
                Forms\Components\TextInput::make('warning_upper')
                    ->label('Dégradé au-dessus de')
                    ->placeholder("Laisser vide pour ignorer")
                    ->nullable()
                    ->numeric(),
                Forms\Components\TextInput::make('danger_upper')
                    ->label('En panne au-dessus de')
                    ->placeholder("Laisser vide pour ignorer")
                    ->nullable()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Metric')
                    ->searchable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('suffix')
                    ->searchable(),
                Tables\Columns\TextColumn::make('warning_under')
                    ->label('Dégradé en-dessous de')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('danger_under')
                    ->label('En panne en-dessous de')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('warning_upper')
                    ->label('Dégradé au-dessus de')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('danger_upper')
                    ->label('En panne au-dessus de')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->before(function (Metric $metric){
                    MetricPoint::where('metric_id', $metric->id)->delete();
                }),
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
            'index' => Pages\ListMetrics::route('/'),
            'create' => Pages\CreateMetric::route('/create'),
            'edit' => Pages\EditMetric::route('/{record}/edit'),
        ];
    }
}
