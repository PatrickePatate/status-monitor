<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class StatusPageController extends Controller
{
    public function show(){
        $services = Service::with(['metric', 'metric.points'])->where('public',true)->get();
        return view('status', [
            'services' => $services
        ]);
    }
}
