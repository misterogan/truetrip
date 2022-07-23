<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DestinationResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'title'         => $this->title,
            'origin'        => $this->origin,
            'destination'   => $this->destination,
            'start'         => $this->start,
            'end'           => $this->end,
            'description'   => $this->description,
            'id'            => $this->id,
        ];
    }
}
