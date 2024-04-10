<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CursosSedesResource extends JsonResource
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
            'sede_id' => $this->sede_id,
            'curso_id' => $this->curso_id,
            'curso' => $this->curso->titulo,
            'sede' => $this->sede->nombre,
            'turno' => $this->curso->turno == "M" ? "Mañana" : "Tarde",
        ];
    }
}
