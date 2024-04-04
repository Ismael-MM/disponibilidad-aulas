<?php

namespace App\Http\Controllers;

use App\Models\CursoSede;
use App\Models\Aula;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Http\Resources\CursosSedesResource;
use App\Http\Resources\CursosResource;
use Inertia\Inertia;

class CursoSedeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
     public function index()
     {
         return Inertia::render('Dashboard/Asignacion');
     }
 
     public function loadItems() 
     {
         $itemsPerPage = Request::get('itemsPerPage', 10);
         $sortBy = json_decode(Request::get('sortBy', '[]'), true);
         $search = json_decode(Request::get('search', '[]'), true);
         $deleted = filter_var(Request::get('deleted', 'false'), FILTER_VALIDATE_BOOLEAN);
 
         $query = CursoSede::query();
 
         if ($deleted) {
             $query->onlyTrashed();
         }
 
         if (!empty($search)) {
             foreach ($search as $key => $value) {
                 if (!empty($value)) {
                    if ($key == 'turno') {
                        $query->whereHas('curso', function ($query) use ($value) {
                            if (preg_match('/^(M|m)/i', $value)) {
                                $value = "M";
                            }else if (preg_match('/^(T|t)/', $value)) {
                                $value = "T";
                            }
                            return $query->where('turno', 'LIKE', '%' . $value . '%');});
                    }else {
                        $query->where($key, 'LIKE', '%' . $value . '%');
                    }
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
 
         $items = CursosSedesResource::collection($query->paginate($itemsPerPage));
 
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
        $curso = Request::get('curso_id');
        $sede = Request::get('sede_id');

        $query = CursoSede::query();

        $duplicado = $query->where('curso_id',$curso)->where('sede_id',$sede)->first();

        if (!is_null($duplicado)) {
            return Redirect::back()->with('warning', 'Esta sede ya tiene vinculado este curso.');
        }

        CursoSede::create(
             Request::validate([
                'curso_id' => ['required'],
                'sede_id' => ['required'],
             ])
         );
 
         return Redirect::back()->with('success', 'Curso creado.');
     }
 
     public function update(CursoSede $asignacion)
     {
        $validatedData = Request::validate([
            'curso_id' => ['required'],
            'sede_id' => ['required'],
        ]);

        $asignacion->update($validatedData);

         return Redirect::back()->with('success', 'Curso editado.');
     }
 
     public function destroy(CursoSede $asignacion)
     {
         $asignacion->delete();
 
         return Redirect::back()->with('success', 'Curso movido a la papelera.');
     }
 
     public function destroyPermanent($id)
     {
         $curso = CursoSede::onlyTrashed()->findOrFail($id);
         $curso->forceDelete();
 
         return Redirect::back()->with('success', 'Curso eliminado de forma permanente.');
     }
 
     public function restore($id)
     {
         $curso = CursoSede::onlyTrashed()->findOrFail($id);
         $curso->restore();
 
         return Redirect::back()->with('success', 'Curso restaurado.');
     }
 
     public function exportExcel()
     {
         $items = CursosSedesResource::collection(CursoSede::all());
 
         return  [ 'itemsExcel' => $items ];
     }

     public function asignacionList()
     {

        $aula = Request::get('aula');
        if (!is_null($aula)) {
            $sede = Aula::find($aula)->id;
        }

        $query = CursoSede::query();

        if (!is_null($sede)) {
            $items = CursosResource::collection($query->where('sede_id', $sede)
            ->with('curso')
            ->get()
            ->pluck('curso'));
        }else {
            $items = CursosSedesResource::collection(CursoSede::all());
        }
        return [ 'lists' => $items];
     }
     
}
