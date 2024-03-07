<?php

namespace App\Models;

use App\Enums\ServiceStatus;
use App\Models\Checks\DnsCheck;
use App\Models\Checks\HttpCheck;
use Illuminate\Database\Eloquent\Casts\AsEnumArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        "status" => ServiceStatus::class
    ];

    public function user(){
        return $this->hasOne(User::class,'id','created_by');
    }

    public function metrics(){
        return $this->hasMany(Metric::class, "service_id");
    }

    public function checks(){
        return $this->http_checks->merge($this->dns_checks);
    }
    public function http_checks(){
        return $this->hasMany(HttpCheck::class);
    }

    public function dns_checks(){
        return $this->hasMany(DnsCheck::class);
    }
}
