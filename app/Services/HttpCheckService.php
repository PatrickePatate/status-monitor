<?php

namespace App\Services;

use App\Enums\ServiceStatus;
use App\Models\Checks\HttpCheck;
use Illuminate\Support\Facades\Http;

class HttpCheckService extends AbstractCheckService
{
    protected $latency = null;

    public function __construct(HttpCheck $check){
        $this->check = $check;
        if(is_null($check)) throw new \Exception("Le check passé au checker est null.");
    }

    public function check(): ServiceStatus
    {
        // Init request
        $request = Http::retry(1)->timeout(env('CHECKER_TIMEOUT',2));
        // If ssl verifying is off, specify it to Guzzle
        if(!$this->check->check_cert){
            $request = $request->withOptions(['verify' => false]);
        }else{
            $this->check->url = str_replace('http://', 'https://', $this->check->url); // force https
            $request = $request->withOptions(['verify' => true]);
        }
        // Adding user headers
        if(!empty($this->check->provide_headers)){
            $request = $request->withHeaders(json_decode($this->check->provide_headers,1)??[]);
        }
        // Executing request
        // GET
        if($this->check->method == "get"){
            try{
                $request = $request->get($this->check->url, @json_decode($this->check->post_args,1)??[]);
            } catch(\Exception $e){
                //Check certificate
                if($this->check->check_cert){
                    return $this->retryWithoutSsl();
                }
                $this->fail = "request";
                $this->fail_message = $e->getMessage();
                return ServiceStatus::OUTAGE;
            }
        // POST
        } elseif($this->check->method == "post") {
            try{
                $request = $request->asForm()->post($this->check->url, @json_decode($this->check->post_args)??[]);
            } catch(\Exception $e){
                //Check certificate
                if($this->check->check_cert){
                    return $this->retryWithoutSsl();
                }
                $this->fail = "request";
                $this->fail_message = $e->getMessage();
                return ServiceStatus::OUTAGE;
            }
        } else { throw new \Exception("Methode incorecte."); }


        // Storing latency
        $this->latency = $request->handlerStats()['total_time'];

        // Check HTTP Code
        if(!is_null($this->check->http_code) && $request->status() !== intval($this->check->http_code)){
            $this->fail = "http_code";
            $this->fail_message = "Le code HTTP ne correspond pas à celui attendu. Code remonté : ".$request->status();
            return ServiceStatus::PARTIAL;
        }

        // Check HTTP Body
        if(!is_null($this->check->http_body) && $request->body() !== $this->check->http_body){
            $this->fail = "http_body";
            $this->fail_message = "Le corps de la requête ne correspond pas à celui attendu. Corps récupéré : ".$request->body();
            return ServiceStatus::PARTIAL;
        }

        return ServiceStatus::AVAILABLE;
    }

    public function metric(): ?float
    {
        return $this->latency;
    }

    public function provideMetric(): bool
    {
        return true;
    }

    public static function metricInfos(): array
    {
        return [
            'name' => "Latence",
            'description' => "Récupère la latence entre le moniteur et le service"
        ];
    }



    private function retryWithoutSsl(){
        $this->check->url = str_replace('https://', 'http://', $this->check->url); // force http
        $request = Http::withoutVerifying()->retry(1)->timeout(env('CHECKER_TIMEOUT',2));

        // adding user headers
        if(!empty($this->check->provide_headers)){
            $request = $request->withHeaders(json_decode($this->check->provide_headers)??[]);
        }
        // executing request
        if($this->check->method == "get"){
            try{
                $request = $request->get($this->check->url, @json_decode($this->check->post_args)??[]);
            } catch(\Exception $e){
                // Without SSL, GET request still fails
                $this->fail = "request";
                $this->fail_message = $e->getMessage();
                return ServiceStatus::OUTAGE;
            }
        } elseif($this->check->method == "post") {
            try{
                $request = $request->asForm()->post($this->check->url, @json_decode($this->check->post_args)??[]);
            } catch(\Exception $e){
                // Without SSL, POST request still fails
                $this->fail = "request";
                $this->fail_message = $e->getMessage();
                return ServiceStatus::OUTAGE;
            }
        } else { throw new \Exception("Methode incorecte."); }

        // If request doesn't fail without SSL verifying, then, SSL cert check failed, returning a PARTIAL status
        logger("SSL certificate check failed", $this->check->toArray());
        $this->fail = "ssl_cert";
        $this->fail_message = "La validation du certificat SSL a échoué.";
        return ServiceStatus::PARTIAL;
    }
}
