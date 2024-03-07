<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Enums\ServiceStatus;
use App\Filament\Resources\ServiceResource;
use App\Models\Service;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;

    protected function handleRecordCreation(array $data): Service
    {
        return static::getModel()::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'status' => ServiceStatus::from($data['status']),
            'created_by' => Auth::user()->id,
            'public' => $data['public'],
            'show_availability' => $data['show_availability']
        ]);
    }
}
