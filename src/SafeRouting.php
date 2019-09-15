<?php

namespace KentarouTakeda\SafeRouting;

use Illuminate\Support\Facades\Route;

class SafeRouting
{
    protected $namespace = '\App\Http\Controllers';

    public function makeRoute(array $array): void {
        if(true !== isset($array['routes'])) {
            return;
        }
        Route::namespace($this->namespace)->group(function() use($array) {
            foreach($array['routes'] as $name => $data) {
                $route = Route::name($name);
                $route->get($data['uri'], $data['controller']);
            }
        });
    }
}
