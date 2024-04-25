<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CursosResource extends JsonResource
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
            'titulo' => $this->titulo,
            'turno' => $this->turno == "M" ? "Mañana" : "Tarde",
            'calidad' => $this->calidad." ⭐",
            'horas' => $this->horas,
            'horas_diarias' => $this->horas_diarias,
            'tituloturno' => $this->titulo . " - " . ($this->turno == "M" ? "Mañana" : "Tarde"),
        ];
    }
}
