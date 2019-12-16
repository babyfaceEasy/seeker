<?php


namespace App\Repositories;

use App\Models\User;
use App\Models\Service;
use App\Constants\Status;
use App\QueryFilters\Sort;
use App\QueryFilters\Active;
use Illuminate\Pipeline\Pipeline;

class UserRepository implements UserRepositoryInterface
{

    /**
     * Returns all the users in the application.
     * @return mixed
     */
    public function all()
    {
        $content = new \StdClass();
        $content->query_class =  User::class;

        $users = app(Pipeline::class)
            ->send($content)
            ->through([
                Active::class,
                Sort::class,
            ])
            ->thenReturn()
            ->paginate(5);
        return $users;
    }

    /**
     * Sets user's status to enabled.
     * @param User $user
     * @return string
     */
    public function enableUser(User $user)
    {
        $user->status = Status::ENABLED;
        if (!$user->save()){
            return Status::ERROR;
        }
        return Status::SUCCESS;
    }

    /**
     * Disabled the user passed.
     * @param User $user
     * @return string
     */
    public function disableUser(User $user)
    {
        $user->status = Status::DISABLED;
        if (!$user->save()){
            return Status::ERROR;
        }

        return Status::SUCCESS;
    }

    /**
     * Adds a service to users saved services collection.
     * @param User $user
     * @param Service $service
     * @return string
     */
    public function saveService(User $user, Service $service)
    {
        $user->savedServices()->attach($service);

        return Status::SUCCESS;
    }

    /**
     * returns user's saved services.
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSavedServices(User $user)
    {
        return $user->savedServices()->get();
    }

    /**
     * Removes a service from users saved service collection.
     * @param User $user
     * @param Service $service
     * @return string
     */
    public function removeService(User $user, Service $service)
    {
        $user->savedServices()->detach($service);

        return Status::SUCCESS;
    }

    /**
     * Gets the details of the given user by checking the DB for the passed ID.
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getUserDetailsByID(int $id)
    {
        $userDetails = User::with('roles:id,name')->find($id);
        return $userDetails;
    }
}