<?php
declare(strict_types=1);

namespace KentarouTakeda\SafeRouting;

use Illuminate\Support\Facades\Route;

class SafeRouting
{

    /** @var Validation */
    protected $validation;

    public function __construct(Validation $validation) {
        $this->validation = $validation;
    }

    public function makeRoute(array $array, int $mtime=null): void {
        if(true !== isset($array['routes'])) {
            return;
        }
        $namespace = $array['namespace'] ?? '\App\Http\Controllers';

        Route::namespace($namespace)->middleware('safe.routing')->group(function() use($array, $mtime) {
            foreach($array['routes'] as $name => $data) {
                $this->applyDefaultData($name, $data);
                if(isset($data['controller'])) {
                    $route = Route::name($name);
                    $methods = array_keys($data['methods']??[]) ?: ['GET'];
                    $methods = array_diff($methods, ['RET']);
                    $route->match($methods, $data['uri'], $data['controller']);
                } else {
                    Route::view($data['uri'], $name)->name($name);
                }

                foreach($data['methods']??[] as $method => $schema) {
                    if(is_null($schema)) {
                        continue;
                    }
                    $this->validation->setSchema($name, $method, $schema, $mtime);
                }
                if(isset($data['RET'])) {
                    $this->validation->setSchema($name, 'RET', $data['RET'], $mtime);
                }
            }
        });
    }

    protected function applyDefaultData(string $name, ?array &$data): void {
        if(true !== isset($data['uri'])) {
            $tmp = str_replace('.', '/', $name);
            $tmp = preg_replace('/(^|\/)index$/', '', $tmp, -1, $count);

            $data['uri'] = $tmp;
        }
    }
}
