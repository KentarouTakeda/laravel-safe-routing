<?php

namespace KentarouTakeda\SafeRouting;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Routing\Router;

class SafeRoutingServiceProvider extends ServiceProvider
{
    private $files = null;

    public function map(SafeRouting $safeRouting)
    {
        foreach($this->getFiles() as $file) {
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

        if(config('app.debug')) {
            $router = resolve(SafeRouting::class);
            foreach($this->getFiles() as $file) {
                foreach(Yaml::parseFile($file)['routes'] ?? [] as $name => $route) {
                    if(isset($route['description'])) {
                        $router->setDescription($name, $route['description']);
                    }
                }
            }
        }

    }

    protected function getFiles(): array {
        if(is_null($this->files)) {
            $glob = base_path('routes/*.yml');
            $this->files = File::glob($glob);
            if($this->files === false) {
                abort(500);
            }
        }
        return $this->files;
    }
}
