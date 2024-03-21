<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MetricPointsResource;
use App\Models\Metric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MetricsController extends Controller
{
    public function metrics($id, Request $req){
        if($req->has('days')) {
            $days = min(intval($req->days),90);
            return MetricPointsResource::collection($this->__daysAvgMetrics($id, $days));
        } elseif($req->has('hours')) {
            $hours = min(intval($req->hours),48);
            return MetricPointsResource::collection($this->__hoursAvgMetrics($id, $hours));
        } else {
            $days = 45;
            return MetricPointsResource::collection($this->__daysAvgMetrics($id, $days));
        }
    }

    private function __daysAvgMetrics($id, $days){
        $metrics = Metric::join('metric_points', 'metrics.id', '=', 'metric_points.metric_id')
            ->join('services', 'metrics.service_id', '=', 'services.id')
            ->select(
                'metrics.id',
                'metrics.name',
                'metrics.service_id',
                'metrics.warning_under',
                'metrics.danger_under',
                'metrics.warning_upper',
                'metrics.danger_upper',
                'metrics.suffix',
                DB::raw('DATE(metric_points.created_at) as date'),
                DB::raw('AVG(CAST(metric_points.value AS decimal)) as average_value')
            )
            ->where('services.show_availability', true)
            ->where('services.public', true)
            ->where('metric_points.created_at', '>=', now()->subDays($days))
            ->where('metrics.id', $id)
            ->groupBy(DB::raw('DATE(metric_points.created_at)'),'metrics.id','services.show_availability')
            ->orderBy('date')
            ->get();

        return $metrics;
    }

    private function __hoursAvgMetrics($id, $hours){
        $metrics = Metric::join('metric_points', 'metrics.id', '=', 'metric_points.metric_id')
            ->join('services', 'metrics.service_id', '=', 'services.id')
            ->select(
                'metrics.id',
                'metrics.name',
                'metrics.service_id',
                'metrics.warning_under',
                'metrics.danger_under',
                'metrics.suffix',
                DB::raw("TO_CHAR(metric_points.created_at, 'YYYY-MM-DD HH24:00:00') as date"),
                DB::raw('AVG(CAST(metric_points.value AS decimal)) as average_value')
            )
            ->where('services.show_availability', true)
            ->where('metric_points.created_at', '>=', now()->subHours($hours))
            ->where('metrics.id', $id)
            ->groupBy(DB::raw("TO_CHAR(metric_points.created_at, 'YYYY-MM-DD HH24:00:00')"),'metrics.id','services.show_availability')
            ->orderBy('date')
            ->get();

        return $metrics;
    }
}
