<?php

namespace App\Providers;

use Filament\Support\Assets\Js;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (env('PROXY_SCHEME') == "https") {
            URL::forceScheme('https');
        }

        FilamentColor::register([
            'danger' => Color::hex('#D52941'),
            'success' => Color::hex('#3DDC97'),
            'warning' => Color::hex('#ffc039'),
            'primary' => Color::hex('#449DD1'),
            'secondary' => Color::hex('#dadada'),
        ]);
        FilamentAsset::register([
            Js::make('local-dates', Vite::asset('resources/js/miscs/local_date.js'))->module(true),
        ]);
    }
}
