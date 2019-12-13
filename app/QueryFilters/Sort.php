<?php


namespace App\QueryFilters;


class Sort extends Filter
{

    protected function applyFilter($content)
    {
        $builder = $content->query_class;
        // check to be sure they passed the right thing.
        if ( !in_array(request('sort'), ['asc', 'desc']) ){
            return $builder;
        }

        $sortKey = strtoupper(request($this->filterName()));
        $sortCol = $builder::getSortableColumn();
        return $builder::query()->orderBy($sortCol, $sortKey);
    }
}