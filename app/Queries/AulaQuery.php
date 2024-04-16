<?php

namespace App\Queries;

class AulaQuery
{

    public function search($search, $query){
        foreach ($search as $key => $value) {
            if (!empty($value)) {
               if ($key == 'sede') {
                   $query->whereHas('sede', function ($query) use ($value) {
                       return $query->where('nombre', 'LIKE', '%' . $value . '%');});
               }else{
                   $query->where($key, 'LIKE', '%' . $value . '%');
               }
               return $query;
            }
        }
    }

    public function sortBy($sortBy, $query){
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

        return $query;
    }

}