<?php

namespace App\Providers;

use Illuminate\Support\Str;
use App\Mixins\StringMixins;
use App\Mixins\ResponseMixins;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //

        // response mixins
        Response::mixin(new ResponseMixins());
        Str::mixin(new StringMixins());
    }
}
