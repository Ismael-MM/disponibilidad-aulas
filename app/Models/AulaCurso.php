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
}
