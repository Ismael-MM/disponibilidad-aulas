<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Festivo extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'festivos';

    protected $fillable = [
        'nombre',
        'fecha',
    ];

    protected $casts = [
        'id' => 'integer',
        'nombre' => 'string',
        'fecha' => 'date',
    ];

}
