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
        <div class="alert alert-{{$global_status->color()}}">
            @switch($global_status)
                @case(App\Enums\ServiceStatus::AVAILABLE)
                    🎉 Tous les composants sont fonctionnels !
                    @break
                @case(App\Enums\ServiceStatus::PARTIAL)
                    🔨 Certains composants fonctionnent de façon dégradés
                    @break
                @case(App\Enums\ServiceStatus::OUTAGE)
                    💥 Certains composants recontrent des problèmes
                @break
            @endswitch
        </div>
        @if($services->pluck('show_availability')->contains(true))
            <div class="metrics-settings-wrapper">
                <div class="metrics-settings-selector">
                    <small class="d-block text-right">Réglages des métrics :</small>
                    <div class="metrics-settings-selector-inputs">
                        <input id="metrics_hours_or_days" type="number" value="45" />
                        <select id="metrics_hours_or_days_type">
                            <option value="days">jours</option>
                            <option value="hours">heures</option>
                        </select>
                    </div>
                </div>
            </div>
        @endif
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
                    @if($service->show_availability && !$service->metrics->isEmpty())
                        @foreach($service->metrics as $metric)
                            <div class="service-availability" data-metric-id="{{$metric->id}}">
                                <div class="service-availability-infos"><span>{{$metric->name}}</span><span class="metrics_span_label"></span></div>
                                <div class="service-availability-chart">
                                    <div class="service-availability-chart-placeholder bg-placeholder"></div>
                                </div>

                            </div>
                        @endforeach

                    @endif
                </div>
            @endforeach
        </div>
    </main>
</body>
</html>
