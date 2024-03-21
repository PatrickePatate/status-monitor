<?php

namespace App\Filament\Resources\Checks\HttpCheckResource\Pages;

use App\Filament\Resources\Checks\HttpCheckResource;
use App\Models\Checks\HttpCheck;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHttpCheck extends CreateRecord
{
    protected static string $resource = HttpCheckResource::class;

    protected function handleRecordCreation(array $data): HttpCheck
    {
        // processing arrays
        $request_args = [];
        $headers = [];
        foreach($data['request_args'] as $d){
            $request_args[$d['key']] = $d['value'];
        }
        foreach($data['provide_headers'] as $d){
            $headers[$d['header_key']] = $d['header_value'];
        }

        return static::getModel()::create([
            'url' => $data['url'],
            'method' => $data['method'],
            'request_args' => json_encode($request_args),
            'service_id' => $data['service_id'],
            'provide_headers' => json_encode($headers),
            'http_code' => $data['http_code'],
            'http_body' => $data['http_body'],
            'check_cert' => $data['check_cert'],
            'metric_id' => $data['metric_id']
        ]);
    }
}
