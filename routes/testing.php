<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::group(['prefix'=>'tests'], function(){
    Route::get('/', function(){
        echo"ok";
    });
    Route::post('/http_checks/post_check', function (Request $req) {
        return response()->json($req->toArray());
    })->name('tests.http_checks.post');
    Route::post('/http_checks/headers_check', function (Request $req) {
        return response()->json($req->headers);
    })->name('tests.http_checks.headers');
});

