<?php

namespace App\Queries;

class DefaultQuery
{
    function search($search, $query) {
        if (!empty($search)) {
            foreach ($search as $key => $value) {
                if (!empty($value)) {
                    $query->where($key, 'LIKE', '%' . $value . '%');
                }
            }
            return $query;
        }
    }

    function sort($sortBy,$query){
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

    public function paginacion($itemsPerPage, $query) {
        if ($itemsPerPage == -1) {
            return $itemsPerPage = $query->count();
        } 
    }

    public function deleted($deleted, $query){
        if ($deleted) {
            return  $query->onlyTrashed();
        }
    }
}