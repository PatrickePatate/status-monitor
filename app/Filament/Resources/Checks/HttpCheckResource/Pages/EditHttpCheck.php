<?php

namespace App\Filament\Resources\Checks\HttpCheckResource\Pages;

use App\Filament\Resources\Checks\HttpCheckResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHttpCheck extends EditRecord
{
    protected static string $resource = HttpCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
