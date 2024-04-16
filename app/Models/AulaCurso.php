<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AulaCurso extends Pivot
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'aula_curso';

    protected $fillable = [
        'fecha_inicio',
        'fecha_fin',
        'curso_id',
        'aula_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class);
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    public static function ValidarFecha($fechaSeleccionadaInicio,$fechaSeleccionadaFin ,$fechaAulaFin, $fechaAulaInicio) {
        if ($fechaAulaInicio == $fechaSeleccionadaInicio) { // comprueba que si la fecha seleccionadaInicios es igual a la de incio de una reserva
            return 1;
        }elseif($fechaAulaFin == $fechaSeleccionadaInicio){ // comprueba que si la fecha seleccionadaInicio es igual a la de fin de una reserva
            return 1;
        }elseif(($fechaSeleccionadaInicio >= $fechaAulaInicio) && ($fechaSeleccionadaInicio <= $fechaAulaFin)){ // comprueba que si la fecha seleccionadaInicio esta entre la fecha inicio y fecha fin de reserva
            return 1;
        }elseif ($fechaAulaInicio == $fechaSeleccionadaFin) { // comprueba que si la fecha seleccionadaFIn es igual a la de incio de una reserva
            return 1;
        }elseif($fechaAulaFin == $fechaSeleccionadaFin){ // comprueba que si la fecha seleccionadaFin es igual a la de fin de una reserva
            return 1;
        }elseif(($fechaSeleccionadaFin >= $fechaAulaInicio) && ($fechaSeleccionadaFin <= $fechaAulaFin)){ // comprueba que si la fecha seleccionadaFin esta entre la fecha inicio y fecha fin de reserva
            return 1;
        }
    }


}
