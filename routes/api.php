<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/metrics/{id}', function ($id, Request $request) {
    $request->validate(['id'=>'exists:App\Models\Metric,id']);
    return \App\Http\Resources\MetricPointsResource::collection(\App\Models\MetricPoint::where('metric_id',$id)->orderBy('created_at','desc')->get());
});

Route::middleware('auth:sanctum')->group(function(){

});

