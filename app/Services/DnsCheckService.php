<?php

namespace App\Services;

use App\Enums\ServiceStatus;
use App\Models\Checks\DnsCheck;
use Spatie\Dns\Dns;
use Spatie\Dns\Records\A;
use Spatie\Dns\Records\AAAA;

class DnsCheckService extends AbstractCheckService
{
    protected DnsCheck $check;
    protected $records = [];

    public function __construct(DnsCheck $check){
        $this->check = $check;
        if(is_null($check)) throw new \Exception("Le check passé au checker est null.");
    }

    public function check(): ServiceStatus
    {
        $dns = new Dns();
        try{
            // Getting records
            $this->records = $dns->getRecords($this->check->domain);

            // Setting up flags
            $check_ipv4 = false;
            $matched_ipv4 = false;
            if(!is_null($this->check->ipv4_match)) {
                $check_ipv4 = true;
            }
            $check_ipv6 = false;
            $matched_ipv6 = false;
            if(!is_null($this->check->ipv6_match)) {
                $check_ipv6 = true;
            }

            // Raising flag
            foreach($this->records as $record){
                if($record instanceof A){
                    if($check_ipv4 && $record->toArray()['ip'] == $this->check->ipv4_match){
                        $matched_ipv4 = true;
                    }
                }
                if($record instanceof AAAA){
                    if($check_ipv6 && $record->toArray()['ipv6'] == $this->check->ipv6_match){
                        $matched_ipv6 = true;
                    }
                }
            }

            if(count($this->records) < 1){
                $this->fail = "no_records";
                return ServiceStatus::OUTAGE;
            }

            // Checking IPV4 match
            if($check_ipv4 && !$matched_ipv4){
                // if ipv4 && ipv6 have no match, return an OUTAGE instead of a PARTIAL
                if($check_ipv6 && !$matched_ipv6){
                    $this->fail = "ipv4_6_match";
                    return ServiceStatus::OUTAGE;
                }
                $this->fail = "ipv4_match";
                return ServiceStatus::PARTIAL;
            }

            // Checking IPV6 match
            if($check_ipv6 && !$matched_ipv6){
                $this->fail = "ipv6_match";
                return ServiceStatus::PARTIAL;
            }

            return ServiceStatus::AVAILABLE;
        }catch(\Exception $e){
            logger($e->getMessage(),$this->check->toArray());
            $this->fail = "request";
            return ServiceStatus::OUTAGE;
        }
    }

    public function metric(): ?float
    {
        return count($this->records);
    }

    public function provideMetric(): bool
    {
        return true;
    }

    public function metricInfos(): array
    {
        return [
          'name' =>  "Nombre d'entrées DNS",
          'description' => "Récupère le nombre d'entrées DNS pour le domaine."
        ];
    }

    public function failed()
    {
        // TODO: Implement failed() method.
    }
}
