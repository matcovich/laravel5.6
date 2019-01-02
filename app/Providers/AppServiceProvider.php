<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\ViewComposers\UserFieldsComposer;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {}

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
