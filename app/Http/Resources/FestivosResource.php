<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class FestivosResource extends JsonResource
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
            'fecha' => Carbon::parse($this->fecha)->format('Y-m-d'),
            'dia' => $this->fecha->day,
            'mes' => $this->fecha->month,
        ];
    }
}
