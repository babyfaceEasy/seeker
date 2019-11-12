<?php


namespace App\Repositories;

use App\Models\User;
use App\Constants\Status;

class UserRepository implements UserRepositoryInterface
{
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
}