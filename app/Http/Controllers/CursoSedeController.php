<?php

namespace App\Http\Controllers;

use App\Models\CursoSede;
use App\Models\Curso;
use App\Models\Aula;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Http\Resources\CursosSedesResource;
use App\Http\Resources\CursosResource;
use App\Http\Requests\CursoSedeRequest;
use Inertia\Inertia;

class CursoSedeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
     public function index()
     {
         return Inertia::render('Dashboard/Gestion/Asignacion');
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
                           $turno = Curso::Turno($value);
                            return $query->where('turno', 'LIKE', '%' . $turno . '%');});
                    }else {
                        $query->where($key, 'LIKE', '%' . $value . '%');
                    }
                 }
             }
         }
 
         if (!empty($sortBy)) {
             foreach ($sortBy as $sort) {
                 if (isset($sort['key']) && isset($sort['order'])) {
                    $order = $sort['order'];
                    if ($sort['key'] == "turno") {
                        $query->join('cursos', 'cursos.id', '=', 'curso_sede.curso_id')
                        ->orderBy('cursos.turno', $order);
                    }elseif ($sort['key'] == "sede") {
                        $query->join('sedes', 'sedes.id', '=', 'curso_sede.sede_id')
                        ->orderBy('sedes.nombre', $order);
                    }elseif ($sort['key'] == "curso") {
                        $query->join('cursos', 'cursos.id', '=', 'curso_sede.curso_id')
                        ->orderBy('cursos.titulo', $order);
                    }else{
                        $query->orderBy($sort['key'], $sort['order']);
                    }
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
 
     public function store(CursoSedeRequest $request)
     {
        $curso = $request->curso_id;
        $sede = $request->sede_id;

        $query = CursoSede::query();

        $duplicado = $query->where('curso_id',$curso)->where('sede_id',$sede)->first();

        if (!is_null($duplicado)) {
            return Redirect::back()->with('warning', 'Esta sede ya tiene vinculado este curso.');
        }

        CursoSede::create($request->validated());
 
         return Redirect::back()->with('success', 'Curso creado.');
     }
 
     public function update(CursoSedeRequest $request,CursoSede $asignacion)
     {

        $asignacion->update($request->validated());

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
            $consulta = Aula::where('id',$aula)->get();
            $sede = $consulta[0]->sede_id;
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
