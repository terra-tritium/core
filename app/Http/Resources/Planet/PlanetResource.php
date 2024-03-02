<?php

namespace App\Http\Resources\Planet;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanetResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'transportShips' => $this->transportShips,
            'name' => $this->name,
            'region'=> $this->region,
            'quadrant' => $this->quadrant,
        ];
    }
}
