<?php

namespace App\Http\Controllers;

use App\Models\Festivo;
use App\Http\Resources\FestivosResource;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class FestivoController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Festivos');
    }

    public function loadItems() 
    {
        $itemsPerPage = Request::get('itemsPerPage', 10);
        $sortBy = json_decode(Request::get('sortBy', '[]'), true);
        $search = json_decode(Request::get('search', '[]'), true);
        $deleted = filter_var(Request::get('deleted', 'false'), FILTER_VALIDATE_BOOLEAN);

        $query = Festivo::query();

        if ($deleted) {
            $query->onlyTrashed();
        }

        if (!empty($search)) {
            foreach ($search as $key => $value) {
                if (!empty($value)) {
                    $query->where($key, 'LIKE', '%' . $value . '%');
                }
            }
        }

        if (!empty($sortBy)) {
            foreach ($sortBy as $sort) {
                if (isset($sort['key']) && isset($sort['order'])) {
                    $query->orderBy($sort['key'], $sort['order']);
                }
            }
        } else {
            $query->orderBy("id", "desc");
        }

        if ($itemsPerPage == -1) {
            $itemsPerPage = $query->count();
        }    

        $items = $query->paginate($itemsPerPage);

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

    public function store()
     {
         Festivo::create(
             Request::validate([
                'nombre' => ['required', 'max:191'],
                'date' => ['required'],
             ])
         );
 
         return Redirect::back()->with('success', 'Festivo creado.');
     }
 
     public function update(Festivo $festivo)
     {
         $festivo->update(
             Request::validate([
                'nombre' => ['required', 'max:191'],
                'date' => ['required'],
             ])
         );
 
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
         $items = Festivo::all();
 
         return  [ 'itemsExcel' => $items ];
     }
     

    public function festivoList()
     {
        $items = FestivosResource::collection(Festivo::all());

        return [ 'lists' => $items];
     }
}
