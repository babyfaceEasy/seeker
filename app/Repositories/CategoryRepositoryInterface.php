<?php

namespace App\Repositories;

interface CategoryRepositoryInterface
{
    public function all();

    public function findById(int $categoryId);

    public function update(int $categoryId);

    public function delete(int $categoryId);
}