<?php

namespace App\Filament\Resources\Checks\DnsCheckResource\Pages;

use App\Filament\Resources\Checks\DnsCheckResource;
use App\Models\Checks\DnsCheck;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDnsCheck extends EditRecord
{
    protected static string $resource = DnsCheckResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $record->update([
            'domain' => $data['domain'],
            'ipv4_match' => $data['ipv4_match']??null,
            'ipv6_match' => $data['ipv6_match']??null,
            'service_id' => $data['service_id'],
            'metric_id' => $data['metric_id']
        ]);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
