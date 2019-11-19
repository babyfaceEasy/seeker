<?php


namespace App\Repositories;


use App\Constants\Status;
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

    public function newRecord($name, $user_id, $category_id, $status = Status::ENABLED)
    {
        $data = compact('name', 'user_id', 'category_id', 'status');
        $record = Service::create($data);

        if (!empty($record)){
            return Status::SUCCESS;
        }

        return Status::ERROR;
    }
}