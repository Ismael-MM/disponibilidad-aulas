<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Festivo;
use App\Http\Resources\FestivosResource;

class FestivoController extends Controller
{
    public function festivoList()
     {
        $items = FestivosResource::collection(Festivo::all());

        return [ 'lists' => $items];
     }
}
