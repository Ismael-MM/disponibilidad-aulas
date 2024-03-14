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
            'curso_id' => $this->curso->titulo,
            'aula_id' => $this->aula->nombre,
            'fecha_fin' => Carbon::parse($this->fecha_fin)->format('Y-m-d'),
            'fecha_inicio' => Carbon::parse($this->fecha_inicio)->format('Y-m-d'),
        ];
    }
}
