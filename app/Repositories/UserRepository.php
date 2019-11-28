<?php


namespace App\Repositories;

use App\Models\User;
use App\Constants\Status;

class UserRepository implements UserRepositoryInterface
{

    /**
     * Returns all the users in the application.
     * @return mixed
     */
    public function all()
    {
        return User::orderBy('status')
            ->get()
            ->map(function ($user){
                return $user->format();
            });
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
}