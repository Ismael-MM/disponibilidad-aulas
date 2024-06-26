<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;

class Aula extends Model
{
    use HasFactory;
    use SoftDeletes, CascadeSoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'aulas';

    protected $cascadeDeletes = ['cursos'];

    protected $fillable = [
        'nombre',
        'calidad',
        'sede_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'sede_id' => 'integer',
    ];

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    public function cursos(): BelongsToMany
    {
        return $this->belongsToMany(Curso::class)->using(AulaCurso::class);
    }
}
