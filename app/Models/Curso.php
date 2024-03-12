<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Curso extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'cursos';

    protected $fillable = [
        'titulo',
        'turno',
        'horas',
        'horas_diarias',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'horas' => 'integer',
        'horas_diarias' => 'integer',
    ];

    public function aulas(): BelongsToMany
    {
        return $this->belongsToMany(Aula::class);
    }

    public function sedes(): BelongsToMany
    {
        return $this->belongsToMany(Sede::class);
    }
}
