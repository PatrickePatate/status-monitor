<?php

namespace App\Filament\Resources\Checks\DnsCheckResource\Pages;

use App\Filament\Resources\Checks\DnsCheckResource;
use App\Services\DnsCheckService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListDnsChecks extends ListRecords
{
    protected static string $resource = DnsCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
