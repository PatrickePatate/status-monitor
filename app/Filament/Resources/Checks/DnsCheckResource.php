<?php

namespace App\Filament\Resources\Checks;

use App\Filament\Resources\Checks\DnsCheckResource\Pages;
use App\Filament\Resources\Checks\DnsCheckResource\RelationManagers;
use App\Models\Checks\DnsCheck;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DnsCheckResource extends Resource
{
    protected static ?string $model = DnsCheck::class;

    protected static ?string $label = "Checks DNS ";

    protected static ?string $navigationGroup = 'Checks';
    protected static ?string $navigationLabel = 'Checks DNS';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('domain')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('service_id')
                    ->relationship(name: 'service', titleAttribute: "name")
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('ipv4_match')
                    ->label('Correspondance IPV4')
                    ->placeholder('Laissez vide pour ne pas vérifier')
                    ->maxLength(255)
                    ->nullable(),
                Forms\Components\TextInput::make('ipv6_match')
                    ->label('Correspondance IPV6')
                    ->placeholder('Laissez vide pour ne pas vérifier')
                    ->maxLength(255)
                    ->nullable(),
                // Dns Checks have a number of records metric
                Forms\Components\Select::make('metric_id')
                    ->relationship(
                        name:'metric',
                        titleAttribute: 'name',
                        modifyQueryUsing: function(Builder $query, $get) { return $query->where('service_id',$get('service_id'))->orWhere('id',$get('metric_id')); },
                    )
                    ->preload()
                    ->searchable()
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('domain')
                    ->label("Domaine")
                    ->searchable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ipv4_match')
                    ->label('Correspondance IPV4')
                    ->default('Ø')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ipv6_match')
                    ->label('Correspondance IPV6')
                    ->default('Ø')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('metric.name')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListDnsChecks::route('/'),
            'create' => Pages\CreateDnsCheck::route('/create'),
            'edit' => Pages\EditDnsCheck::route('/{record}/edit'),
        ];
    }
}
