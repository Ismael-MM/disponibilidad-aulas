<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SedeResource;

class AulasResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'sede_id' => $this->sede_id,
            'aulasede' => $this-> nombre . " - " . $this->sede->nombre,
            'sede' => $this->sede->nombre,
   
        ];
    }
}
