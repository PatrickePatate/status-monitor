<div>
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
    @if(!$service->metrics->isEmpty())
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
