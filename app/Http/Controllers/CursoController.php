<?php

namespace App\Http\Controllers;

use App\Http\Requests\CursoStoreRequest;
use App\Http\Requests\CursoUpdateRequest;
use App\Models\Curso;
use Illuminate\Support\Facades\Redirect;
use App\Http\Resources\CursosResource;
use Illuminate\Support\Facades\Request;
use App\Queries\CursoQuery;
use App\Queries\DefaultQuery;
use Inertia\Inertia;

class CursoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
     public function index()
     {
         return Inertia::render('Dashboard/Gestion/Cursos');
     }
 
     public function loadItems() 
     {
         $itemsPerPage = Request::get('itemsPerPage', 10);
         $sortBy = json_decode(Request::get('sortBy', '[]'), true);
         $search = json_decode(Request::get('search', '[]'), true);
         $deleted = filter_var(Request::get('deleted', 'false'), FILTER_VALIDATE_BOOLEAN);
 
         $consultas = new CursoQuery();
         $consultasDefault = new DefaultQuery();
         $query = Curso::query();
 
         $consultasDefault->deleted($deleted, $query);
         $consultasDefault->paginacion($itemsPerPage, $query);

         $consultas->search($search, $query);
         $consultas->sort($sortBy, $query);
 
         $items = CursosResource::collection($query->paginate($itemsPerPage));
 
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

     public function loadAutocompleteItems() {
        $search = Request::get('search', '');

        $items = CursosResource::collection(Curso::whereRaw("CONCAT(titulo, ' (', id, ')') LIKE '%$search%'")->limit(6)->get());

        return ['autocompleteItems' => $items];

    }
 
     public function store(CursoStoreRequest $request)
     {
         Curso::create(
             $request->validated()
         );
 
         return Redirect::back()->with('success', 'Curso creado.');
     }
 
     public function update(CursoUpdateRequest $request,Curso $curso)
     {
        $valueTurno = $request->turno;
        $turno = Curso::Turno($valueTurno);

        $validatedData = $request->validated();

        $validatedData['turno'] = $turno;


        $curso->update($validatedData);

         return Redirect::back()->with('success', 'Curso editado.');
     }
 
     public function destroy(Curso $curso)
     {
         $curso->delete();
 
         return Redirect::back()->with('success', 'Curso movido a la papelera.');
     }
 
     public function destroyPermanent($id)
     {
         $curso = Curso::onlyTrashed()->findOrFail($id);
         $curso->forceDelete();
 
         return Redirect::back()->with('success', 'Curso eliminado de forma permanente.');
     }
 
     public function restore($id)
     {
         $curso = Curso::onlyTrashed()->findOrFail($id);
         $curso->restore();
 
         return Redirect::back()->with('success', 'Curso restaurado.');
     }
 
     public function exportExcel()
     {
         $items = CursosResource::collection(Curso::all());
 
         return  [ 'itemsExcel' => $items ];
     }

     public function cursosList()
     {
        $items = CursosResource::collection(Curso::all());

        return [ 'lists' => $items];
     }
     
}
