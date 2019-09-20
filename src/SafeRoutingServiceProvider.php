<?php

namespace KentarouTakeda\SafeRouting;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Routing\Router;

class SafeRoutingServiceProvider extends ServiceProvider
{
    public function map(SafeRouting $safeRouting)
    {
        $glob = base_path('routes/*.yml');
        $files = File::glob($glob);
        if($files === false) {
            abort(500);
        }
        foreach($files as $file) {
            $array = Yaml::parseFile($file);
            $mtime = File::lastModified($file);
            $safeRouting->makeRoute($array, $mtime);
        }

        if(config('app.debug')) {
                $this->loadViewsFrom(__DIR__.'/resources/views', 'saferouting');
            Route::prefix('_saferouting')
                ->namespace(__NAMESPACE__ . '\Http\Controllers')
                ->group(__DIR__ . '/routes/web.php');
        }
    }

    public function register()
    {
        parent::register();
        $this->app->singleton(SafeRouting::class);
        $this->app->singleton(Validation::class);
    }

    public function boot()
    {
        parent::boot();

        $router = resolve(Router::class);
        $router->middlewareGroup('safe.routing', [
            ApplyView::class,
            Validation::class,
        ]);
    }
}
