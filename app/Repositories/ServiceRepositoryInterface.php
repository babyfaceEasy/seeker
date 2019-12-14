<?php


namespace App\Repositories;


interface ServiceRepositoryInterface
{
    public function all();

    public function getUserServices(int $user_id);

    public function newRecord(string $name, int $user_id, int $category_id, string $status);

    public function update(int $category_id, array $data);

    public function delete(int $service_id);

    public function search(string $service_name);
}