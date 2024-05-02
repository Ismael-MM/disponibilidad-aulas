<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\Curso;
use DB;
use Carbon\Carbon;
use App\Http\Resources\AulasResource;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Http\Requests\AulaRequest;
use App\Queries\AulaQuery;
use App\Queries\DefaultQuery;
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

        $consultas = new AulaQuery();
        $consultasDefault = new DefaultQuery();
        $query = Aula::query();

        $consultas->search($search, $query);
        $consultas->sortBy($sortBy, $query);

        $consultasDefault->deleted($deleted, $query);
        $consultasDefault->paginacion($itemsPerPage, $query);

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

    public function update(AulaRequest $request, Aula $aula)
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

        return  ['itemsExcel' => $items];
    }

    public function aulasList()
    {

        $sede = Request::get('sede');
        $query = Aula::query();

        if (!is_null($sede)) {
            $items = AulasResource::collection($query->where('sede_id', $sede)
                ->get());
        } else {
            $items = AulasResource::collection(Aula::all());
        }

        return ['lists' => $items];
    }

    public function aulasLibres()
    {
        // Obtener las fechas de inicio y fin del cuerpo de la solicitud
        $fechaInicio = str_replace("'", "", Request::get('fecha_inicio'));
        $fechaFin = str_replace("'", "", Request::get('fecha_fin'));
        $sedeId = Request::get('sedeId');
        $cursoId = Request::get('cursoId');

        // Obtener el turno del curso
        $turnoCurso = Curso::findOrFail($cursoId)->turno;

        // Filtrar las aulas disponibles para el curso, las fechas seleccionadas y el turno del curso
        $aulasDisponibles = DB::select('
        SELECT *
        FROM aulas
        WHERE aulas.sede_id = :sedeId
        AND `aulas`.`deleted_at` is null
        AND NOT EXISTS (
            SELECT 1
            FROM aula_curso
            INNER JOIN cursos ON aula_curso.curso_id = cursos.id
            WHERE aula_curso.aula_id = aulas.id
            AND cursos.turno = :turnoCurso
            AND (
                (aula_curso.fecha_inicio BETWEEN :fechaInicio AND :fechaFin)
                OR (aula_curso.fecha_fin BETWEEN :fechaInicio AND :fechaFin)
                OR (:fechaInicio BETWEEN aula_curso.fecha_inicio AND aula_curso.fecha_fin)
            )
        )
    ', [
        'sedeId' => $sedeId,
        'turnoCurso' => $turnoCurso,
        'fechaInicio' => $fechaInicio,
        'fechaFin' => $fechaFin
    ]);

        return ['lists' => AulasResource::collection($aulasDisponibles)];
    }
}
