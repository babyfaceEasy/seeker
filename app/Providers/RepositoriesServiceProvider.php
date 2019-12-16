<?php

namespace App\Providers;

use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\ServiceRepository;
use App\Repositories\BookingRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\ServiceRepositoryInterface;
use App\Repositories\BookingRepositoryInterface;
use App\Repositories\CategoryRepositoryInterface;

class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);
    }
}
