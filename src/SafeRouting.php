<?php
declare(strict_types=1);

namespace KentarouTakeda\SafeRouting;

use Illuminate\Support\Facades\Route;

class SafeRouting
{

    /** @var Validation */
    protected $validation;

    /** @var array */
    protected $descriptions = [];

    /** @var string[] */
    const METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    public function __construct(Validation $validation) {
        $this->validation = $validation;
    }

    public function makeRoute(array $array, int $mtime=null): void {
        if(true !== isset($array['routes'])) {
            return;
        }
        $prefix = $array['prefix'] ?? '/';
        $namespace = $array['namespace'] ?? '\App\Http\Controllers';
        $middlewares = array_merge(['safe.routing'], $array['middlewares']??[]);

        Route::prefix($prefix)->namespace($namespace)->middleware($middlewares)->group(function() use($array, $mtime) {
            foreach($array['routes'] as $name => $data) {
                $this->applyDefaultData($name, $data);

                $methods = array_intersect(self::METHODS, array_keys($data['methods']??[]) ?: ['GET']);
                $defaultMethod = null;

                foreach($methods as $method) {
                    $controller = $data['methods'][$method]['controller'] ?? $data['controller'] ?? null;
                    $routeName = $name;
                    if(isset($controller)) {
                        if(isset($defaultMethod)) {
                            $routeName .= ".__{$method}__";
                        } else {
                            $defaultMethod = $method;
                        }
                        $route = Route::name($routeName);
                        if(isset($data['middlewares'])) {
                            $route->middleware($data['middlewares']);
                        }
                        $route->match($method, $data['uri'], $controller);
                    } else {
                        $route = Route::view($data['uri'], $name)->name($name);
                        if(isset($data['middlewares'])) {
                            $route->middleware($data['middlewares']);
                        }
                        break;
                    }
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

    public function setDescription(string $name, string $description): void {
        $this->descriptions[$name] = $description;
    }
    public function getDescription(string $name):? string {
        return $this->descriptions[$name] ?? null;
    }

    static function NameWithoutMethod(string $name): string {
        foreach(self::METHODS as $m) {
            $name = preg_replace("/\.__{$m}__$/", '', $name, 1, $count);
            if($count>0) {
                break;
            }
        }
        return $name;
    }
}
