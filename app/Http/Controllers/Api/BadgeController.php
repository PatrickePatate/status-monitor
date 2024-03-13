<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Cachet\Badger\Facades\Badger;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    public function badge(int $service, Request $req){
        $service = Service::where('public',true)->findOrFail($service);

        switch($service->status->color()){
            case 'success':
                $color = "green";
                break;
            case 'warning':
                $color = "yellow";
                break;
            case 'danger':
                $color = 'red';
                break;
            case 'secondary':
            default:
                $color = 'lightgray';
                break;
        }
        $type = ($req->has('type') && in_array($req->type, ['flat-square', 'plastic-flat', 'flat', 'plastic', 'social'])) ? $req->type : 'plastic-flat';
        return response(
            str_replace('xlink:href="undefined"','xlink:href="'.env('APP_URL').'"',
                Badger::generate($service->name, $service->status->label()??'N/A', $color, $type)
            ), 200, [
            'Content-Type' => 'image/svg+xml'
            ]
        );
    }
}
