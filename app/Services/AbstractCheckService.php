<?php

namespace App\Services;

use App\Enums\ServiceStatus;

abstract class AbstractCheckService
{
    protected $fail = null;
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
    public abstract function metricInfos():array;

    public function failed(){
        return $this->fail;
    }
}
