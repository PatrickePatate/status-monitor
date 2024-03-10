<?php

namespace App\Models\Checks;

use App\Enums\ServiceStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckError extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = "uuid";
    protected $casts = [
        'from_status' => ServiceStatus::class,
        'to_status' => ServiceStatus::class
    ];
    protected $guarded = [];

    public function check(){
        return $this->morphTo('check');
    }
}
