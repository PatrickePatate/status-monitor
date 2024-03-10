<?php

namespace App\Filament\Widgets;

use App\Enums\ServiceStatus;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Features\SupportAttributes\AttributeCollection;

class GlobalStatusWidget extends BaseWidget
{
    protected static ?int $sort = -2;

    protected static bool $isLazy = false;
    protected int | string | array $columnSpan = 1;
    protected static string $view = 'filament.widgets.global-status-widget';
    protected function getViewData(): array
    {
        $partial = false; $outage = false;
        Service::all()->each(function($item) use(&$partial,&$outage) {
            if($item->status == ServiceStatus::PARTIAL) $partial = true;
            if($item->status == ServiceStatus::OUTAGE) $outage = true;
        });

        $global_status = ($partial && $outage) ? ServiceStatus::OUTAGE : ((!$partial && !$outage) ? ServiceStatus::AVAILABLE : ServiceStatus::PARTIAL);
        return ['global_status' => $global_status];
    }
}
