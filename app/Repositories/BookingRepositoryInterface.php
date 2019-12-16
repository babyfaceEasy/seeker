<?php


namespace App\Repositories;


interface BookingRepositoryInterface
{
    public function all();
    public function bookService(array $details);
}