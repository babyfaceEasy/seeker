<?php


namespace App\Repositories;


interface ServiceRepositoryInterface
{
    public function all();

    public function newRecord(string $name, int $user_id, int $category_id, string $status);
}