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
            'curso_id' => $this->curso->titulo,
            'sede_id' => $this->sede->nombre,
            'turno' => $this->curso->turno == "M" ? "Mañana" : "Tarde",
        ];
    }
}
