<?php


namespace App\Repositories;


use App\Models\Service;
use App\Constants\Status;
use App\QueryFilters\Sort;
use App\QueryFilters\Active;
use Illuminate\Pipeline\Pipeline;

class ServiceRepository implements ServiceRepositoryInterface
{
    /**
     * Returns all the services available in the system.
     * @return mixed
     */
    public function all()
    {
        $content = new \StdClass();
        $content->query_class =  Service::class;
        // nb: through calls the array in reverse, so the last class is called first and also
        // data returned from one class is passed to the next class
        $services = app(Pipeline::class)
            ->send($content)
            ->through([
                Active::class,
                Sort::class,
            ])
            ->thenReturn()
            ->paginate(5);
        return $services;
    }

    /**
     * Returns all the services, belonging to user passed.
     * @param int $user_id
     * @return mixed
     */
    public function getUserServices(int $user_id)
    {
        return Service::where('user_id', $user_id)->orderBy('name')->get()->map(
            function ($service){
                return $service->format();
            }
        );
    }

    /**
     * Assists in creating a new service for the user.
     * @param string $name of the service.
     * @param int $user_id owner of the service
     * @param int $category_id the category the service belongs to
     * @param string $status
     * @return string
     */
    public function newRecord($name, $user_id, $category_id, $status = Status::ENABLED)
    {
        $data = compact('name', 'user_id', 'category_id', 'status');
        $record = Service::create($data);

        if (!empty($record)){
            return Status::SUCCESS;
        }

        return Status::ERROR;
    }

    /**
     * This assists with updating a service details.
     * @param int $service_id Service ID
     * @param array $data Information to be updated.
     * @return string | array
     */
    public function update(int $service_id, array $data)
    {
        $service = Service::find($service_id);
        if (empty($service)){
            return Status::ERROR;
        }
        if ($service->update($data)){
            return $service->fresh()->format();
        }

        return Status::ERROR;
    }

    /**
     * Deletes a given service.
     * @param int $service_id service ID
     * @return string
     */
    public function delete(int $service_id)
    {
        $service = Service::find($service_id);

        if (empty($service)){
            return Status::ERROR;
        }

        if ($service->delete()){
            return Status::SUCCESS;
        }

        return Status::ERROR;
    }

}