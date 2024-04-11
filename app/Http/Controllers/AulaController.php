<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Http\Resources\AulasResource;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Http\Requests\AulaRequest;
use Inertia\Inertia;

class AulaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
     public function index()
     {
         return Inertia::render('Dashboard/Gestion/Aulas');
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
                    if ($key == 'sede') {
                        $query->whereHas('sede', function ($query) use ($value) {
                            return $query->where('nombre', 'LIKE', '%' . $value . '%');});
                    }else{
                        $query->where($key, 'LIKE', '%' . $value . '%');
                    }
                 }
             }
         }
 
         if (!empty($sortBy)) {
             foreach ($sortBy as $sort) {
                 if (isset($sort['key']) && isset($sort['order'])) {
                    $order = $sort['order'];

                    if ($sort['key'] == 'sede') {
                        $query->join('sedes', 'sedes.id', '=', 'aulas.sede_id')
                        ->select('aulas.id', 'aulas.nombre', 'aulas.sede_id', 'sedes.nombre AS sede_nombre')
                        ->orderBy('sedes.nombre', $order);
                    }else {
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
 
         $items = AulasResource::collection($query->paginate($itemsPerPage));
 
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
 
     public function store(AulaRequest $request)
     {
         Aula::create(
            $request->validated()
         );
 
         return Redirect::back()->with('success', 'Aula creada.');
     }
 
     public function update(AulaRequest $request,Aula $aula)
     {
         $aula->update(
             $request->validated()
         );
 
         return Redirect::back()->with('success', 'Aula editada.');
     }
 
     public function destroy(Aula $aula)
     {
         $aula->delete();
 
         return Redirect::back()->with('success', 'Aula movida a la papelera.');
     }
 
     public function destroyPermanent($id)
     {
         $aula = Aula::onlyTrashed()->findOrFail($id);
         $aula->forceDelete();
 
         return Redirect::back()->with('success', 'Aula eliminada de forma permanente.');
     }
 
     public function restore($id)
     {
         $aula = Aula::onlyTrashed()->findOrFail($id);
         $aula->restore();
 
         return Redirect::back()->with('success', 'Aula restaurada.');
     }
 
     public function exportExcel()
     {
         $items = AulasResource::collection(Aula::all());
 
         return  [ 'itemsExcel' => $items ];
     }

     public function aulasList()
     {
        $items = AulasResource::collection(Aula::all());

        return [ 'lists' => $items];
     }
     
     
}
