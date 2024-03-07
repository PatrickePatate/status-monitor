<?php

namespace App\Jobs;

use App\Models\Checks\DnsCheck;
use App\Models\Checks\HttpCheck;
use App\Models\Service;
use App\Services\DnsCheckService;
use App\Services\HttpCheckService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        Service::with(['http_checks', 'dns_checks'])->get()->each(function($service){
            try{
                $service->http_checks->each(function($check){
                    $checker = (new HttpCheckService($check));
                    $res = $checker->check();
                    $check->service->status = $res;
                    if($check->service->isDirty('status')){
                        $check->service?->update(['status'=>$res]);
                    }
                    if(!is_null($check->metric) && $checker->provideMetric()){
                        $check->metric->addPoint($checker->metric());
                    }
                    //todo: implementer un système de retour d'erreur
                });
                $service->dns_checks->each(function($check){
                    $checker = (new DnsCheckService($check));
                    $res = $checker->check();
                    $check->service->status = $res;
                    if($check->service->isDirty('status')){
                        $check->service?->update(['status'=>$res]);
                    }
                    if(!is_null($check->metric) && $checker->provideMetric()){
                        $check->metric->addPoint($checker->metric());
                    }
                    //todo: implementer un système de retour d'erreur
                });
            }catch(\Exception $e){
                logger('EMERGENCY WHILE CHECKING SERVICE: '.$e->getMessage(), $service->toArray());
            }

        });

    }
}
