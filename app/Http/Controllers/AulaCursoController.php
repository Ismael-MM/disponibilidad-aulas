<?php

namespace App\Http\Controllers;

use App\Models\AulaCurso;
use App\Http\Resources\AulasCursosResource;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class AulaCursoController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/ReservarAula');
    }

    public function loadItems() 
    {
        $itemsPerPage = Request::get('itemsPerPage', 10);
        $sortBy = json_decode(Request::get('sortBy', '[]'), true);
        $search = json_decode(Request::get('search', '[]'), true);
        $deleted = filter_var(Request::get('deleted', 'false'), FILTER_VALIDATE_BOOLEAN);

        $query = AulaCurso::query();

        if ($deleted) {
            $query->onlyTrashed();
        }

        if (!empty($search)) {
            foreach ($search as $key => $value) {
                if (!empty($value)) {
                    if ($key == 'aula_id') {
                        $query->whereHas('aula', function ($query) use ($value) {
                            return $query->where('nombre', 'LIKE', '%' . $value . '%');});
                    }else if ($key == 'curso_id') {
                        $query->whereHas('curso', function ($query) use ($value) {
                            return $query->where('titulo', 'LIKE', '%' . $value . '%');});
                    }else if ($key == 'sede') {
                        $query->whereHas('aula.sede', function ($query) use ($value) {
                            return $query->where('nombre', 'LIKE', '%' . $value . '%');});
                    }else if ($key == 'turno') {
                        $query->whereHas('curso', function ($query) use ($value) {
                            if (preg_match('/^(M|m)/i', $value)) {
                                $value = "M";
                            }else if (preg_match('/^(T|t)/', $value)) {
                                $value = "T";
                            }
                            return $query->where('turno', 'LIKE', '%' . $value . '%');});
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
                    if ($sort['key'] == "sede") {
                        $query->whereHas('aula.sede', function ($query) use ($order) {
                            $query->orderBy("nombre", $order);});
                    }else if ($sort['key'] == "turno") {
                        $query->whereHas('curso', function ($query) use ($order) {
                            $query->orderBy("turno", $order);});
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

        $items = AulasCursosResource::collection($query->paginate($itemsPerPage));

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
        AulaCurso::create(
            Request::validate([
                'aula_id' => ['required', 'exists:aulas,id'],
                'curso_id' => ['required', 'exists:cursos,id'],
                'fecha_inicio' => ['required', 'date'],
                'fecha_fin' => ['required', 'date'],
            ])
        );

        return Redirect::back()->with('success', 'Aula reservada.');
    }

    public function update(AulaCurso $reservas)
    {
        $reservas->update(
            Request::validate([
                'aula_id' => ['required', 'exists:aulas,id'],
                'curso_id' => ['required', 'exists:cursos,id'],
                'fecha_inicio' => ['required', 'date'],
                'fecha_fin' => ['required', 'date'],
            ])
        );

        return Redirect::back()->with('success', 'Reserva editada.');
    }

    public function destroy(AulaCurso $reservas)
    {
        $reservas->delete();

        return Redirect::back()->with('success', 'Reserva movida a la papelera.');
    }

    public function destroyPermanent($id)
    {
        $reservas = AulaCurso::onlyTrashed()->findOrFail($id);
        $reservas->forceDelete();

        return Redirect::back()->with('success', 'Reserva eliminada de forma permanente.');
    }

    public function restore($id)
    {
        $reservas = AulaCurso::onlyTrashed()->findOrFail($id);
        $reservas->restore();

        return Redirect::back()->with('success', 'Reserva restaurada.');
    }

    public function exportExcel()
    {
        $items = AulaCurso::all();

        return  [ 'itemsExcel' => $items ];
    }
}
