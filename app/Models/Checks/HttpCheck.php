<?php

namespace App\Models\Checks;

use App\Models\Metric;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HttpCheck extends Model
{
    use HasFactory;

    protected $casts = [
        'last_checked_at' => 'datetime'
    ];
    protected $guarded = [];

    public function service(){
        return $this->belongsTo(Service::class);
    }

    public function metric(){
        return $this->belongsTo(Metric::class, );
    }
}
