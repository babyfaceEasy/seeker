<?php


namespace App\Repositories;


use App\Models\User;
use App\Models\Service;

interface UserRepositoryInterface
{
    public function enableUser(User $user);
    public function disableUser(User $user);
    public function all();
    public function saveService(User $user, Service $service);
    public function removeService(User $user, Service $service);
    public function getSavedServices(User $user);
    public function getUserDetailsByID(int $id);
}