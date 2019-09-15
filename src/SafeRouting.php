<?php

namespace KentarouTakeda\SafeRouting;

use Illuminate\Support\Facades\Route;

class SafeRouting
{
    public function makeRoute(array $array): void {
        if(true !== isset($array['routes'])) {
            return;
        }
        $namespace = $array['namespace'] ?? '\App\Http\Controllers';
        Route::namespace($namespace)->group(function() use($array) {
            foreach($array['routes'] as $name => $data) {
                if(isset($data['controller'])) {
                    $route = Route::name($name);
                    $route->get($data['uri'], $data['controller']);
                } else {
                    Route::view($data['uri'], $name)->name($name);
                }
            }
        });
    }
}
