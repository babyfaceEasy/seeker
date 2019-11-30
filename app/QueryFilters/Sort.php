<?php


namespace App\QueryFilters;

use App\Models\Service;

class Sort extends Filter
{

    protected function applyFilter($builder)
    {
        // check to be sure they passed the right thing.
        if ( !in_array(request('sort'), ['asc', 'desc']) ){
            return $builder;
        }

        $sortKey = strtoupper(request($this->filterName()));
        return $builder::query()->orderBy($builder::getSortableColumn(), $sortKey);
    }
}