<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AulasCursosResource extends JsonResource
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
            'curso_id' => $this->curso->id,
            'aula_id' => $this->aula->id,
            'aula' => $this->aula->nombre,
            'curso' => $this->curso->titulo,
            'sede' => $this->aula->sede->nombre,
            'turno' => $this->curso->turno == "M" ? "Mañana" : "Tarde",
            'fecha_fin' => Carbon::parse($this->fecha_fin)->format('Y-m-d'),
            'fecha_fin_dia_mas' => Carbon::parse($this->fecha_fin)->addDay()->format('Y-m-d'),
            'fecha_inicio' => Carbon::parse($this->fecha_inicio)->format('Y-m-d'),
            'infoReserva' => $this->aula->nombre . ' - ' . $this->curso->titulo . ' - ' . Carbon::parse($this->fecha_fin)->format('d/m/Y'),

        ];
    }
}
