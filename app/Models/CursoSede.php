<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class CursoSede extends Pivot
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'curso_sede';

    protected $fillable = [
        'curso_id',
        'sede_id',
    ];

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }
}
