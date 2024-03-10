<?php

namespace App\Filament\Resources\Checks\DnsCheckResource\Pages;

use App\Filament\Resources\Checks\DnsCheckResource;
use App\Models\Checks\DnsCheck;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDnsCheck extends CreateRecord
{
    protected static string $resource = DnsCheckResource::class;

    protected function handleRecordCreation(array $data): DnsCheck
    {

        return static::getModel()::create([
            'domain' => $data['domain'],
            'ipv4_match' => $data['ipv4_match']??null,
            'ipv6_match' => $data['ipv6_match']??null,
            'service_id' => $data['service_id'],
            'metric_id' => $data['metric_id']
        ]);
    }
}
