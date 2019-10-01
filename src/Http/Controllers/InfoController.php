<?php

namespace KentarouTakeda\SafeRouting\Http\Controllers;

use Illuminate\Support\Facades\Route;
use KentarouTakeda\SafeRouting\SafeRouting;

class InfoController {
    public function list(SafeRouting $routing) {
        $routes = [];

        foreach(Route::getRoutes() as $route) {
            if(!in_array('safe.routing', $middleware = $route->gatherMiddleware())) {
                continue;
            }

            $middleware = array_filter($middleware, function($in) { return $in !== 'safe.routing'; } );
            $middleware = array_values($middleware);

            $methods = array_diff($route->methods, ['HEAD']);
            $methods = array_values($methods);

            $name = $route->getName();
            $routes[] = [
                'name' => $name,
                'methods' => $methods,
                'uri' => $route->uri(),
                'parameters' => $route->parameterNames(),
                'middleware' => $middleware,
                'description' => $routing->getDescription($name),
            ];
        }

        return view('saferouting::list', [
            'routes' => $routes,
        ]);
    }
}
