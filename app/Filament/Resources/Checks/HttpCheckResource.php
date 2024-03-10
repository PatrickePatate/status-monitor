<?php

namespace App\Filament\Resources\Checks;

use App\Filament\Resources\Checks\HttpCheckResource\Pages;
use App\Filament\Resources\Checks\HttpCheckResource\RelationManagers;
use App\Models\Checks\HttpCheck;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HttpCheckResource extends Resource
{
    protected static ?string $model = HttpCheck::class;
    protected static ?string $label = "Checks HTTP ";
    protected static ?string $navigationGroup = 'Checks';
    protected static ?string $navigationLabel = 'Checks HTTP';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('url')
                    ->placeholder("Url de votre service avec http(s)://")
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('method')
                    ->label('Méthode')
                    ->options([
                        'get'=>'get',
                        'post'=>'post'
                    ])
                    ->required(),
                Forms\Components\Select::make('service_id')
                    ->relationship(name: 'service', titleAttribute: 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('http_code')
                    ->label("Code HTTP attendu - laissez vide pour ne rien vérifier")
                    ->default(200)
                    ->nullable()
                    ->numeric(),
                Forms\Components\Textarea::make('http_body')
                    ->label('Réponse attendue - laissez vide pour ne rien vérifier')
                    ->nullable()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('check_cert')
                    ->label('Vérifier le certificat SSL ?')
                    ->required(),
                Fieldset::make('parameters')
                    ->label('Parametrez la requête')
                    ->schema([
                        Repeater::make('request_args')
                            ->label('Arguments de la requête')
                            ->schema([
                                Forms\Components\TextInput::make('key')->label('Clé'),
                                Forms\Components\TextInput::make('value')->label('Valeur')
                            ])->columnSpan(2),
                        Repeater::make('provide_headers')
                            ->label('Headers')
                            ->schema([
                                Forms\Components\TextInput::make('header_key')->label('Clé'),
                                Forms\Components\TextInput::make('header_value')->label('Valeur')
                            ])->columnSpan(2),
                    ]),
                // HttpChecks have latency metric
                Forms\Components\Select::make('metric_id')
                    ->label('Métric associée au check')
                    ->relationship(
                        name:'metric',
                        titleAttribute: 'name',
                        modifyQueryUsing: function(Builder $query, $get) { return $query->where('service_id',$get('service_id'))->orWhere('id',$get('metric_id')); },
                    )
                    ->searchable()
                    ->preload()
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('service.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('method')
                    ->label('Méthode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('http_code')
                    ->label('Code HTTP attendu')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('check_cert')
                    ->label('Vérifier le certificat SSL')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('metric.name')
                    ->label('Metric associée')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListHttpChecks::route('/'),
            'create' => Pages\CreateHttpCheck::route('/create'),
            'edit' => Pages\EditHttpCheck::route('/{record}/edit'),
        ];
    }
}
