<?php

namespace KentarouTakeda\SafeRouting;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Yaml\Yaml;

class SafeRoutingServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = '\App\Http\Controllers';

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $glob = base_path('routes/*.yml');
        $files = File::glob($glob);
        if($files === false) {
            abort(404);
        }

        foreach($files as $file) {
            $yaml = File::get($file);
            $array = Yaml::parse($yaml);

            if(true !== isset($array['routes'])) {
                continue;
            }

            Route::namespace($this->namespace)->group(function() use($array) {
                foreach($array['routes'] as $name => $data) {
                    $route = Route::name($name);
                    $route->get($data['uri'], $data['controller']);
                }
            });
        }
    }
}
