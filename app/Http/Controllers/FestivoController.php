<?php

namespace App\Http\Controllers;

use App\Models\Festivo;
use App\Http\Resources\FestivosResource;
use App\Http\Requests\FestivoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Queries\DefaultQuery;
use Inertia\Inertia;

class FestivoController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Calendario/Festivos');
    }

    public function loadItems() 
    {
        $itemsPerPage = Request::get('itemsPerPage', 10);
        $sortBy = json_decode(Request::get('sortBy', '[]'), true);
        $search = json_decode(Request::get('search', '[]'), true);
        $deleted = filter_var(Request::get('deleted', 'false'), FILTER_VALIDATE_BOOLEAN);

        $consultas = new DefaultQuery();
        $query = Festivo::query();

        $consultas->deleted($deleted, $query);
        $consultas->search($search, $query);
        $consultas->sort($sortBy, $query);
        $consultas->paginacion($itemsPerPage, $query);

        $items = FestivosResource::collection($query->paginate($itemsPerPage));

        return [
            'tableData' => [
                'items' => $items->items(),
                'itemsLength' => $items->total(),
                'itemsPerPage' => $items->perPage(),
                'page' => $items->currentPage(),
                'sortBy' => $sortBy,
                'search' => $search, 
                'deleted' => $deleted,
            ]
        ];
    }

    public function store(FestivoRequest $request)
     {
         Festivo::create($request->validated());
 
         return Redirect::back()->with('success', 'Festivo creado.');
     }
 
     public function update(FestivoRequest $request ,Festivo $festivo)
     {
         $festivo->update($request->validated());
 
         return Redirect::back()->with('success', 'Festivo editado.');
     }
 
     public function destroy(Festivo $festivo)
     {
         $festivo->delete();
 
         return Redirect::back()->with('success', 'Festivo movido a la papelera.');
     }
 
     public function destroyPermanent($id)
     {
         $festivo = Festivo::onlyTrashed()->findOrFail($id);
         $festivo->forceDelete();
 
         return Redirect::back()->with('success', 'Festivo eliminado de forma permanente.');
     }
 
     public function restore($id)
     {
         $festivo = Festivo::onlyTrashed()->findOrFail($id);
         $festivo->restore();
 
         return Redirect::back()->with('success', 'Festivo restaurado.');
     }
 
     public function exportExcel()
     {
         $items = FestivosResource::collection(Festivo::all());
 
         return  [ 'itemsExcel' => $items ];
     }
     

    public function festivoList()
     {
        $items = FestivosResource::collection(Festivo::all());

        return [ 'lists' => $items];
     }
}
