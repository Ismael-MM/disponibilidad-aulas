<?php

namespace App\Queries;

use App\Models\Curso;

class AulaCursoQuery
{

    public function search($search, $query)
    {
        if (!empty($search)) {
            foreach ($search as $key => $value) {
                if (!empty($value)) {
                    if ($key == 'aula') {
                        $query->whereHas('aula', function ($query) use ($value) {
                            return $query->where('nombre', 'LIKE', '%' . $value . '%');
                        });
                    } else if ($key == 'curso') {
                        $query->whereHas('curso', function ($query) use ($value) {
                            return $query->where('titulo', 'LIKE', '%' . $value . '%');
                        });
                    } else if ($key == 'sede') {
                        $query->whereHas('aula.sede', function ($query) use ($value) {
                            return $query->where('nombre', 'LIKE', '%' . $value . '%');
                        });
                    } else if ($key == 'turno') {
                        $query->whereHas('curso', function ($query) use ($value) {
                            $turno = Curso::Turno($value);
                            return $query->where('turno', 'LIKE', '%' . $turno . '%');
                        });
                    } else {
                        $query->where($key, 'LIKE', '%' . $value . '%');
                    }
                }
            }
        }
        return $query;
    }

    public function sort($sortBy, $query)
    {
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
                        $query->join('cursos as cursoTitulo','cursoTitulo.id', '=', 'aula_curso.curso_id')
                        ->orderBy('cursoTitulo.titulo',$order);
                    }elseif ($sort['key'] == "aula") {
                        $query->join('aulas as aulaNombre','aulaNombre.id', '=', 'aula_curso.aula_id')
                        ->orderBy('aulaNombre.nombre',$order);
                    }else{
                        $query->orderBy($sort['key'], $sort['order']);
                    }
                }
            }
        } else {
            $query->orderBy("id", "desc");
        }

        return $query;
    }
}
