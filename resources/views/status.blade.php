<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{env('APP_NAME')}}</title>


    <!-- Styles -->
    @vite(['resources/scss/page.scss', 'resources/js/page.js'])
</head>
<body>
    <main>
        <div class="alert alert-success">
            🎉 Tous les composants sont fonctionnels !
        </div>
        <div class="services">
            @foreach($services as $service)
                <div class="service">
                    <div class="service-core">
                        <div class="service-name">
                            <p class="text-bold">{{$service->name}}</p>
                            <div class="service-description">
                                <p class="service-description text-light">{{$service->description}}</p>
                            </div>
                        </div>
                        <div class="badge badge-outline badge-{{$service->status->color()}}">
                            <div class="dot dot-{{$service->status->color()}}"></div>
                            {{$service->status->label()}}
                        </div>
                    </div>
                    @if($service->show_availability && !is_null($service->metric))
                        <div class="service-availability" data-metric-id="{{$service->metric?->id}}">
                            <span class="text-light" style='font-style: italic;'>Le graphique de disponibilité est en train de charger...</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </main>
</body>
</html>
