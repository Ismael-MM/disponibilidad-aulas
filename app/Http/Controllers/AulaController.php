<?php

namespace App\Http\Controllers;

use App\Http\Requests\CursoStoreRequest;
use App\Http\Requests\CursoUpdateRequest;
use App\Models\Aula;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class AulaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
     public function index()
     {
         return Inertia::render('Dashboard/Aulas');
     }
 
     public function loadItems() 
     {
         $itemsPerPage = Request::get('itemsPerPage', 10);
         $sortBy = json_decode(Request::get('sortBy', '[]'), true);
         $search = json_decode(Request::get('search', '[]'), true);
         $deleted = filter_var(Request::get('deleted', 'false'), FILTER_VALIDATE_BOOLEAN);
 
         $query = Aula::query();
 
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
 
         $items = $query->with('sede')->paginate($itemsPerPage);
 
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
         Aula::create(
             Request::validate([
                'nombre' => ['required', 'max:191'],
                'sede_id' => ['required', 'max:191'],
             ])
         );
 
         return Redirect::back()->with('success', 'Aula creado.');
     }
 
     public function update(Aula $aula)
     {
         $aula->update(
             Request::validate([
                'nombre' => ['required', 'max:191'],
                'sede_id' => ['required', 'max:191'],
             ])
         );
 
         return Redirect::back()->with('success', 'Aula editado.');
     }
 
     public function destroy(Aula $aula)
     {
         $aula->delete();
 
         return Redirect::back()->with('success', 'Aula movido a la papelera.');
     }
 
     public function destroyPermanent($id)
     {
         $aula = Aula::onlyTrashed()->findOrFail($id);
         $aula->forceDelete();
 
         return Redirect::back()->with('success', 'Aula eliminado de forma permanente.');
     }
 
     public function restore($id)
     {
         $aula = Aula::onlyTrashed()->findOrFail($id);
         $aula->restore();
 
         return Redirect::back()->with('success', 'Aula restaurado.');
     }
 
     public function exportExcel()
     {
         $items = Aula::all();
 
         return  [ 'itemsExcel' => $items ];
     }
     
}
