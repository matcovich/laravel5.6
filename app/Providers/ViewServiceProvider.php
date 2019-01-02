<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blade::component('shared._card', 'card');

        Blade::directive('render', function ($expression){
            $parts = explode(',', $expression, 2);
            $component = $parts[0];
            $args = trim($parts[1] ?? '[]');
            return "<?php echo app('App\Http\ViewComponents\\\\'.{$component}, {$args})->toHtml() ?>";
        });

    }

    public function register()
    {
        //
    }
}
