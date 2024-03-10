<?php

namespace App\Jobs;

use App\Enums\ServiceStatus;
use App\Models\Checks\CheckError;
use App\Models\Checks\DnsCheck;
use App\Models\Checks\HttpCheck;
use App\Models\Service;
use App\Services\AbstractCheckService;
use App\Services\DnsCheckService;
use App\Services\HttpCheckService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
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
                    $prev_status = $check->service?->status;
                    $checker = (new HttpCheckService($check));
                    $res = $checker->check();
                    logger('HTTP CHECK de '.$checker->getCheck()->service?->name.PHP_EOL."Résultat: ".$res->label());
                    $this->process_status($check, $res);

                    if(!is_null($check->metric) && $checker->provideMetric()){
                        $check->metric->addPoint($checker->metric());
                    }
                    // process errors
                    $this->process_error($check, $checker, $prev_status);
                });
                $service->dns_checks->each(function($check){
                    $prev_status = $check->service?->status;
                    $checker = (new DnsCheckService($check));
                    $res = $checker->check();
                    $this->process_status($check, $res);

                    if(!is_null($check->metric) && $checker->provideMetric()){
                        $check->metric->addPoint($checker->metric());
                    }
                    // process errors
                    $this->process_error($check, $checker, $prev_status);
                });
            }catch(\Exception $e){
                logger('EMERGENCY WHILE CHECKING SERVICE: '.$e->getMessage(), $service->toArray());
            }

        });

    }

    private function process_status(Model $model, $status){
        $model->service->status = $status;
        if($model->service->isDirty('status')){
            $model->service?->update([
                'status'=>$status,
                'last_checked_at' => Carbon::now()
            ]);
        }else{
            $model->service->update(['last_checked_at' => Carbon::now()]);
        }
    }

    private function process_error(Model $model, AbstractCheckService $checker, $previous_status){
        if(!is_null($checker->failed())){
            $last_error = CheckError::where('service_id', $checker->getCheck()?->service?->id)->where('check_type',$checker->getCheck()::class)->where('check_id',$checker->getCheck()?->id)->orderBy('created_at','desc')->limit(1)->first();
            if($last_error?->to_status == $checker->getCheck()->service?->status->value && $last_error?->error_type == $checker->failed()){
                // avoid setting multiple errors for the same problem
                return;
            }
            $error = new CheckError();
            $error->fill([
                'service_id' => $checker->getCheck()?->service?->id,
                'from_status' => $previous_status,
                'to_status' => $checker->getCheck()->service?->status,
                'error_type' => $checker->failed(),
                'error_message' => $checker->error(),
            ]);
            $error->check()->associate($checker->getCheck());
            $error->save();
        }
    }
}
