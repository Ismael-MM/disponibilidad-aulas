<?php

namespace App\Http\Controllers;

use App\Models\AulaCurso;
use App\Models\Curso;
use App\Models\Aula;
use DB;
use App\Http\Resources\AulasCursosResource;
use App\Http\Requests\AulaCursoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Queries\DefaultQuery;
use App\Queries\AulaCursoQuery;
use Carbon\Carbon;
use Inertia\Inertia;

class AulaCursoController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Calendario/ReservarAula');
    }

    public function loadItems()
    {
        $itemsPerPage = Request::get('itemsPerPage', 10);
        $sortBy = json_decode(Request::get('sortBy', '[]'), true);
        $search = json_decode(Request::get('search', '[]'), true);
        $deleted = filter_var(Request::get('deleted', 'false'), FILTER_VALIDATE_BOOLEAN);

        $consultas = new AulaCursoQuery();
        $consultasDefault = new DefaultQuery();
        $query = AulaCurso::query();


        $consultasDefault->deleted($deleted, $query);
        $consultasDefault->paginacion($itemsPerPage, $query);

        $consultas->search($search, $query);
        $consultas->sort($sortBy, $query);


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

    public function store(AulaCursoRequest $request)
    {
        $query = AulaCurso::query();

        $fechaSeleccionadaInicio = Carbon::parse($request->fecha_inicio);
        $fechaSeleccionadaFin = Carbon::parse($request->fecha_fin);

        $curso = Curso::where('id',  $request->curso_id)->get();
        $turno = $curso[0]->turno;

        $aulas = $query->join('cursos', 'cursos.id', '=', 'aula_curso.curso_id')
            ->where('aula_id', Request::get('aula_id'))
            ->where('cursos.turno', $turno)
            ->get();

        // funcion para comprar si el dia seleccionado esta disponible.
        foreach ($aulas as $aula) {

            $fechaAulaInicio = $aula->fecha_inicio;
            $fechaAulaFin = $aula->fecha_fin;
            $validacionFechas = 0;

            $validacionFechas = AulaCurso::ValidarFecha($fechaSeleccionadaInicio, $fechaSeleccionadaFin, $fechaAulaFin, $fechaAulaInicio);

            if ($validacionFechas == 1) {
                return Redirect::back()->with('warning', 'La fecha de inicio de esta reserva entra en conflicto con otra programada para el mismo período.');
            }
        }

        AulaCurso::create(
            $request->validated()
        );

        return Redirect::back()->with('success', 'Aula reservada.');
    }

    public function update(AulaCursoRequest $request, AulaCurso $reservas)
    {
        $reservas->update(
            $request->validated()
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
        $items = AulasCursosResource::collection(AulaCurso::all());

        return  ['itemsExcel' => $items];
    }


    public function reservasList(AulaCurso $reserva)
    {
        $items = '';
        $valueTurno = Request::get('turno');
        $sede = Request::get('sede');
        $query = AulaCurso::query();

        if (Request::get('sede') != null && Request::get('turno') != null) {


            $turno = Curso::Turno($valueTurno);

            $items = AulasCursosResource::collection(
                $query->join('cursos', 'cursos.id', '=', 'aula_curso.curso_id')
                    ->join('aulas', 'aulas.id', '=', 'aula_curso.aula_id')
                    ->join('sedes', 'sedes.id', '=', 'aulas.sede_id')
                    ->where('cursos.turno', $turno)
                    ->where('sedes.nombre', $sede)
                    ->get()
            );
        } elseif (Request::get('turno') != null) {

            $turno = Curso::Turno($valueTurno);

            $items = AulasCursosResource::collection(
                $query->join('cursos', 'cursos.id', '=', 'aula_curso.curso_id')
                    ->where('cursos.turno', $turno)
                    ->get()
            );
        } else {
            $items = AulasCursosResource::collection(AulaCurso::all());
        }

        return ['lists' => $items];
    }

    public function freeAulas()
    {
        $query = AulaCurso::query();

        $sede = Request::get('sede');
        $fechaInicio = Carbon::parse(Request::get('inicio'));
        $fechaFin = Carbon::parse(Request::get('fin'));

        $aulas = Aula::where('sede_id', $sede)->get();

        $freeAulas = [];

        foreach ($aulas as $aula) {
            // Consultamos si existe algún registro en la tabla 'aula_curso' que se superponga con el período dado.
            $overlap = DB::table('aula_curso')
                ->where('aula_id', $aula->id)
                ->where(function ($query) use ($fechaInicio, $fechaFin) {
                    $query->where(function ($query) use ($fechaInicio, $fechaFin) {
                        $query->where('fecha_inicio', '<=', $fechaFin)
                            ->where('fecha_fin', '>=', $fechaInicio);
                    })
                    ->orWhere(function ($query) use ($fechaInicio, $fechaFin) {
                        $query->where('fecha_inicio', '>=', $fechaInicio)
                            ->where('fecha_inicio', '<=', $fechaFin);
                    })
                    ->orWhere(function ($query) use ($fechaInicio, $fechaFin) {
                        $query->where('fecha_fin', '>=', $fechaInicio)
                            ->where('fecha_fin', '<=', $fechaFin);
                    });
                })
                ->exists();
        
            if (!$overlap) {
                // Si no hay superposición, el aula está libre.
                $freeAulas[] = $aula;
            }
        }

        return ['lists' => $freeAulas];
    }
}
