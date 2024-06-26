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
        foreach($data['request_args'] as $d){
            if(!empty($d['key']) && !empty($d['value'])){
                $request_args[$d['key']] =$d['value'];
            }
        }
        foreach($data['provide_headers'] as $d){
            if(!empty($d['header_key']) && !empty($d['header_value'])){
                $headers[$d['header_key']] = $d['header_value'];
            }
        }
        $data['provide_headers'] = $headers;
        $data['request_args'] = $request_args;

        $record->update([
            'url' => $data['url'],
            'method' => $data['method'],
            'request_args' => $data['request_args'],
            'service_id' => $data['service_id'],
            'provide_headers' => $data['provide_headers'],
            'http_code' => $data['http_code'],
            'http_body' => $data['http_body'],
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
