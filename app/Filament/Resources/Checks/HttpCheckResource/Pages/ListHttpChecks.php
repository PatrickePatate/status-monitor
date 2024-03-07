<?php

namespace App\Filament\Resources\Checks\HttpCheckResource\Pages;

use App\Filament\Resources\Checks\HttpCheckResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHttpChecks extends ListRecords
{
    protected static string $resource = HttpCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
