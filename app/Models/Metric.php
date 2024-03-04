<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metric extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function points() {
        return $this->hasMany(Metric::class, "id", "metric_id");
    }

    public function service() {
        return $this->hasOne(Service::class);
    }
}
