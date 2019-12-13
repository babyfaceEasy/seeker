<?php


namespace App\QueryFilters;

use App\Constants\Status;
class Active extends Filter
{
    protected function applyFilter($builder)
    {
        $status = (request('active') == 1) ? Status::ENABLED : Status::DISABLED;
        return $builder->where('status', $status);
    }
}