<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServicesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $service = parent::toArray($request);
        $service['status'] = $this->status;
        $service['status_label'] = $this->status?->label();
        $service['status_state'] = $this->status?->color();
        foreach(($copy = $service['metrics']) as $key => $met){
            if($service['show_availability'] === false){
                unset($service['metrics'][$key]);
            }
        }
        return $service;
    }
}
