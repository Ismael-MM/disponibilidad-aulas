<?php

namespace App\Http\Controllers;

use App\Models\AulaCurso;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class AulaCursoController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/AulasCursos');
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
        AulaCurso::create(
            Request::validate([
                'nombre' => ['required', 'max:191'],
            ])
        );

        return Redirect::back()->with('success', 'AulaCurso creado.');
    }

    public function update(AulaCurso $aulacurso)
    {
        $aulacurso->update(
            Request::validate([
            ])
        );

        return Redirect::back()->with('success', 'AulaCurso editado.');
    }

    public function destroy(AulaCurso $aulacurso)
    {
        $aulacurso->delete();

        return Redirect::back()->with('success', 'AulaCurso movido a la papelera.');
    }

    public function destroyPermanent($id)
    {
        $aulacurso = AulaCurso::onlyTrashed()->findOrFail($id);
        $aulacurso->forceDelete();

        return Redirect::back()->with('success', 'AulaCurso eliminado de forma permanente.');
    }

    public function restore($id)
    {
        $aulacurso = AulaCurso::onlyTrashed()->findOrFail($id);
        $aulacurso->restore();

        return Redirect::back()->with('success', 'AulaCurso restaurado.');
    }

    public function exportExcel()
    {
        $items = AulaCurso::all();

        return  [ 'itemsExcel' => $items ];
    }
}
