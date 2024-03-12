<?php

namespace App\Http\Controllers;

use App\Http\Requests\CursoStoreRequest;
use App\Http\Requests\CursoUpdateRequest;
use App\Models\Sede;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SedeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
     public function index()
     {
         return Inertia::render('Dashboard/Sedes');
     }
 
     public function loadItems() 
     {
         $itemsPerPage = Request::get('itemsPerPage', 10);
         $sortBy = json_decode(Request::get('sortBy', '[]'), true);
         $search = json_decode(Request::get('search', '[]'), true);
         $deleted = filter_var(Request::get('deleted', 'false'), FILTER_VALIDATE_BOOLEAN);
 
         $query = Sede::query();
 
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
         Sede::create(
             Request::validate([
                'nombre' => ['required', 'max:191'],
             ])
         );
 
         return Redirect::back()->with('success', 'Suscriptor creado.');
     }
 
     public function update(Sede $sede)
     {
         $sede->update(
             Request::validate([
                'nombre' => ['required', 'max:191'],
             ])
         );
 
         return Redirect::back()->with('success', 'Suscriptor editado.');
     }
 
     public function destroy(Sede $sede)
     {
         $sede->delete();
 
         return Redirect::back()->with('success', 'Curso movido a la papelera.');
     }
 
     public function destroyPermanent($id)
     {
         $sede = Sede::onlyTrashed()->findOrFail($id);
         $sede->forceDelete();
 
         return Redirect::back()->with('success', 'Suscriptor eliminado de forma permanente.');
     }
 
     public function restore($id)
     {
         $sede = Sede::onlyTrashed()->findOrFail($id);
         $sede->restore();
 
         return Redirect::back()->with('success', 'Suscriptor restaurado.');
     }
 
     public function exportExcel()
     {
         $items = Sede::all();
 
         return  [ 'itemsExcel' => $items ];
     }
     
}
