<?php

namespace KentarouTakeda\SafeRouting\Http\Controllers;

use Illuminate\Support\Facades\Route;

class InfoController {
    public function list() {
        $routes = [];

        foreach(Route::getRoutes() as $route) {
            if(!in_array('safe.routing', $middleware = $route->gatherMiddleware())) {
                continue;
            }

            $middleware = array_filter($middleware, function($in) { return $in !== 'safe.routing'; } );
            $middleware = array_values($middleware);

            $routes[] = [
                'name' => $route->getName(),
                'methods' => $route->methods,
                'uri' => $route->uri(),
                'parameters' => $route->parameterNames(),
                'middleware' => $middleware,
            ];
        }

        return view('saferouting::list', [
            'routes' => $routes,
        ]);
    }
}
