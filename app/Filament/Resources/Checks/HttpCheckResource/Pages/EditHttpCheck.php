<?php

namespace App\Filament\Resources\Checks\HttpCheckResource\Pages;

use App\Filament\Resources\Checks\HttpCheckResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditHttpCheck extends EditRecord
{
    protected static string $resource = HttpCheckResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {

        // processing arrays
        $request_args = [];
        $headers = [];
        foreach(json_decode($data['request_args'],1) as $key => $d){
            $request_args[] = ['key' => $key, "value" => $d];
        }
        foreach(json_decode($data['provide_headers'],1) as $key => $d){
            $headers[] = ["header_key" => $key, "header_value" => $d];
        }
        $data['provide_headers'] = $headers;
        $data['request_args'] = $request_args;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // processing arrays
        $request_args = [];
        $headers = [];
//        foreach(json_decode($data['request_args'],1) as $d){
//            $request_args[] = ['key' => $d['key'], "value" => $d['value']];
//        }
//        foreach(json_decode($data['provide_headers'],1) as $d){
//            $headers[] = ["header_key" => $d['header_key'], "header_value" => $d['header_value']];
//        }

        $record->update([
            'url' => $data['url'],
            'method' => $data['method'],
            'request_args' => $request_args,
            'service_id' => $data['service_id'],
            'provide_headers' => $headers,
            'http_code' => $data['http_code'],
            'check_cert' => $data['check_cert'],
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
