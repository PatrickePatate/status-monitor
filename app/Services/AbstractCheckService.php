<?php

namespace App\Services;

use App\Enums\ServiceStatus;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractCheckService
{
    protected $fail = null;
    protected $fail_message = '';
    protected $check = null;
    /**
     * Check service status.
     * @return ServiceStatus
     */
    public abstract function check():ServiceStatus;

    /**
     * return service metric (from previous check)
     * @return float|null
     */
    public abstract function metric():?float;

    public abstract function provideMetric():bool;

    /**
     * Metric Infos should return an array with 2 keys :
     * Name: Metric Name
     * Description : What is the metric
     * @return array
     */
    public abstract static function metricInfos():array;

    public function getCheck():Model{
        return $this->check;
    }

    public function failed(){
        return $this->fail;
    }

    public function error(){
        return $this->fail_message;
    }
}
