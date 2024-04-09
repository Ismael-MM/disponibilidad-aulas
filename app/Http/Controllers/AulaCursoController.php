<?php

namespace App\Http\Controllers;

use App\Models\AulaCurso;
use App\Models\Curso;
use App\Http\Resources\AulasCursosResource;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
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

        $query = AulaCurso::query();

        if ($deleted) {
            $query->onlyTrashed();
        }

        if (!empty($search)) {
            foreach ($search as $key => $value) {
                if (!empty($value)) {
                    if ($key == 'aula') {
                        $query->whereHas('aula', function ($query) use ($value) {
                            return $query->where('nombre', 'LIKE', '%' . $value . '%');});
                    }else if ($key == 'curso') {
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
                        $query->join('aulas', 'aulas.id', '=', 'aula_curso.aula_id')
                        ->join('sedes', 'sedes.id', '=', 'aulas.sede_id')
                        ->orderBy('sedes.nombre', $order);
                    }else if ($sort['key'] == "turno") {
                        $query->join('cursos','cursos.id', '=', 'aula_curso.curso_id')
                        ->orderBy('cursos.turno',$order);
                    }elseif ($sort['key'] == "curso") {
                        $query->join('cursos','cursos.id', '=', 'aula_curso.curso_id')
                        ->orderBy('cursos.titulo',$order);
                    }elseif ($sort['key'] == "aula") {
                        $query->join('aulas','aulas.id', '=', 'aula_curso.aula_id')
                        ->orderBy('aulas.nombre',$order);
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
        $query = AulaCurso::query();

        $fechaSeleccionada = Carbon::parse(Request::get('fecha_inicio'));

        $curso = Curso::where('id', Request::get('curso_id'))->get();
        $turno = $curso[0]->turno;
        
        $aulas = $query->join('cursos','cursos.id', '=', 'aula_curso.curso_id')
                        ->where('aula_id',Request::get('aula_id'))
                        ->where('cursos.turno', $turno)
                        ->get();

        // funcion para comprar si el dia seleccionado esta disponible.
        foreach ($aulas as $aula){

            $fechaAulaInicio = $aula->fecha_inicio;
            $fechaAulaFin = $aula->fecha_fin;

            if ($fechaAulaInicio == $fechaSeleccionada) { // comprueba que si la fecha seleccionada es igual a la de incio de una reserva
                return Redirect::back()->with('warning', 'La fecha de inicio de esta reserva entra en conflicto con otra programada para el mismo período.');
            }elseif($fechaAulaFin == $fechaSeleccionada){ // comprueba que si la fecha seleccionada es igual a la de fin de una reserva
                return Redirect::back()->with('warning', 'La fecha de inicio de esta reserva entra en conflicto con otra programada para el mismo período.');
            }elseif(($fechaSeleccionada >= $fechaAulaInicio) && ($fechaSeleccionada <= $fechaAulaFin)){ // comprueba que si la fecha seleccionada esta entre la fecha inicio y fecha fin de reserva
                return Redirect::back()->with('warning', 'La fecha de inicio de esta reserva entra en conflicto con otra programada para el mismo período.');
            }
        }

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
        $items = AulasCursosResource::collection(AulaCurso::all());

        return  [ 'itemsExcel' => $items ];
    }

    
    public function reservasList(AulaCurso $reserva)
    {
        $items = '';
        $turno = Request::get('turno');
        $sede = Request::get('sede');

        if (Request::get('sede') != null && Request::get('turno') != null) {


            if (preg_match('/^(M|m)/i', $turno)) {
                $turno = "M";
            }else if (preg_match('/^(T|t)/', $turno)) {
                $turno = "T";
            }

            $items = DB::select(
                "SELECT aula_curso.id,aula_curso.fecha_inicio,aula_curso.fecha_fin,aula_curso.deleted_at,cursos.titulo,cursos.turno,
                aulas.nombre AS aula_id,sedes.nombre AS sede_id
                FROM aula_curso
                JOIN cursos JOIN aulas JOIN sedes ON aula_curso.curso_id = cursos.id AND aula_curso.aula_id = aulas.id AND aulas.sede_id = sedes.id
                where cursos.turno = '$turno' AND sedes.nombre = '$sede' AND aula_curso.deleted_at IS NULL;"
            );

        }elseif (Request::get('turno') != null){

            if (preg_match('/^(M|m)/i', $turno)) {
                $turno = "M";
            }else if (preg_match('/^(T|t)/', $turno)) {
                $turno = "T";
            }

            $items = DB::select("SELECT aula_curso.id,aula_curso.fecha_inicio,aula_curso.fecha_fin,aula_curso.deleted_at,cursos.titulo,cursos.turno,aulas.nombre
                AS aula_id,sedes.nombre AS sede_id FROM aula_curso JOIN cursos JOIN aulas JOIN sedes
                ON aula_curso.curso_id = cursos.id AND aula_curso.aula_id = aulas.id AND aulas.sede_id = sedes.id
                where cursos.turno = '$turno' AND aula_curso.deleted_at IS NULL;");
        }else {
            $items = AulasCursosResource::collection(AulaCurso::all());
        }

       return [ 'lists' => $items];
    }
}
