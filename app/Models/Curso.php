<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;

class Curso extends Model
{
    use HasFactory;
    use SoftDeletes, CascadeSoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'cursos';
    
    protected $cascadeDeletes = ['aulas'];

    protected $fillable = [
        'titulo',
        'codigo',
        'turno',
        'horas',
        'horas_diarias',
        'calidad',
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
        return $this->belongsToMany(Aula::class)->using(AulaCurso::class);
    }

    public static function Turno($turno){
        if (preg_match('/^(M|m)/i', $turno)) {
            $turno = "M";
        }else if (preg_match('/^(T|t)/', $turno)) {
            $turno = "T";
        }
        return $turno;
    }
}
