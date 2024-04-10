<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;

class Sede extends Model
{
    use HasFactory;
    use SoftDeletes, CascadeSoftDeletes;

    protected $table = 'sedes';

    protected $cascadeDeletes = ['aulas', 'cursos'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    public function cursos(): BelongsToMany
    {
        return $this->belongsToMany(Curso::class);
    }

    public function aulas(): HasMany
    {
        return $this->hasMany(Aula::class);
    }
}
