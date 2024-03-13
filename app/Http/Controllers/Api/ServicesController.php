<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServicesResource;
use App\Models\Service;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function services(){
        return response()->json(ServicesResource::collection(Service::with('metrics')->where('services.public', true)->get()));
    }
}
