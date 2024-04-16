<?php

namespace App\Queries;

use App\Models\Curso;

class CursoQuery
{

    function search($search, $query) {
        if (!empty($search)) {
            foreach ($search as $key => $value) {
                if (!empty($value)) {
                   if ($key == 'turno') {
                       $turno = Curso::Turno($value);
                       $query->where('turno', 'LIKE', '%' . $turno . '%');;
                   }else {
                       $query->where($key, 'LIKE', '%' . $value . '%');
                   }
                }
                return $query;
            }
        }
    }

    function sort($sortBy, $query){
        if (!empty($sortBy)) {
            foreach ($sortBy as $sort) {
                if (isset($sort['key']) && isset($sort['order'])) {
                    $query->orderBy($sort['key'], $sort['order']);
                }
            }
        } else {
            $query->orderBy("id", "desc");
        }
        return $query;

    }
}