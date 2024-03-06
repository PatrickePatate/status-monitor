<?php

namespace App\Http\Controllers;

use App\Enums\ServiceStatus;
use App\Models\Service;
use Illuminate\Http\Request;

class StatusPageController extends Controller
{
    public function show(){
        $services = Service::with(['metric', 'metric.points'])->where('public',true)->get();

        $partial = false; $outage = false;
        $services->each(function($item) use(&$partial,&$outage) {
          if($item->status == ServiceStatus::PARTIAL) $partial = true;
          if($item->status == ServiceStatus::OUTAGE) $outage = true;
        });

        $global_status = ($partial && $outage) ? ServiceStatus::OUTAGE : ((!$partial && !$outage) ? ServiceStatus::AVAILABLE : ServiceStatus::PARTIAL);

        return view('status', [
            'services' => $services,
            'global_status' => $global_status
        ]);
    }
}
