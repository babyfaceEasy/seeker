<?php


namespace App\Repositories;


use App\Models\Service;

class ServiceRepository implements ServiceRepositoryInterface
{
    public function all()
    {
        return Service::orderBy('name')->get()->map(
            function ($service){
                return $service->format();
            }
        );
    }
}