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

    public function makeRoute(array $array): void {
        if(true !== isset($array['routes'])) {
            return;
        }
        $namespace = $array['namespace'] ?? '\App\Http\Controllers';

        $middlewares = [
            Validation::class,
            ApplyView::class,
        ];

        Route::namespace($namespace)->middleware($middlewares)->group(function() use($array) {
            foreach($array['routes'] as $name => $data) {
                $this->applyDefaultData($name, $data);
                if(isset($data['controller'])) {
                    $route = Route::name($name);
                    $route->get($data['uri'], $data['controller']);
                } else {
                    Route::view($data['uri'], $name)->name($name);
                }

                foreach($data['validation']??[] as $method => $schema) {
                    $this->validation->setSchema($name, $method, $schema);
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
