<?php


namespace App\Repositories;


use App\Models\User;

interface UserRepositoryInterface
{
    public function enableUser(User $user);
    public function disableUser(User $user);
    public function all();
}