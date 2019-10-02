<?php
declare(strict_types=1);

namespace KentarouTakeda\SafeRouting;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;

use JsonSchema\Validator;
use JsonSchema\Constraints\Constraint as C;
use JsonSchema\Exception\ValidationException;

use Closure;

class Validation
{
    private const OPT_REQUEST = C::CHECK_MODE_TYPE_CAST | C::CHECK_MODE_EXCEPTIONS | C::CHECK_MODE_COERCE_TYPES | C::CHECK_MODE_APPLY_DEFAULTS;
    private const OPT_RESPONSE = C::CHECK_MODE_TYPE_CAST | C::CHECK_MODE_EXCEPTIONS;

    const METHOD_OTHER = [
        Request::METHOD_POST,
        Request::METHOD_PUT,
        Request::METHOD_PATCH,
        Request::METHOD_DELETE,
        Request::METHOD_PURGE,
        Request::METHOD_OPTIONS,
        Request::METHOD_TRACE,
        Request::METHOD_CONNECT,
    ];

    /** @var Validator */
    protected $validator;

    /** @var array */
    protected $schemas = [];

    /** @var string */
    protected $cachePath;

    public function __construct(Validator $validator) {
        $this->validator = $validator;
        $this->cachePath = storage_path('framework/saferouting/');
        File::makeDirectory($this->cachePath, 0775, true, true);
    }

    public function handle(Request $request, Closure $next)
    {
        $routename = Route::currentRouteName();
        $method = $request->method();
        $schema = $this->getSchema($routename, 'GET');

        if($schema) {
            try {
                $query = $this->validate($request->query(), $schema, self::OPT_REQUEST);
            } catch(ValidationException $e) {
                abort(400, $e->getMessage());
            }
            $request->query->replace($query);
        }

        foreach(self::METHOD_OTHER as $method) {
            $schema = $this->getSchema($routename, $method);
            if(is_null($schema)) {
                continue;
            }
            try {
                $post = $this->validate($request->post(), $schema, self::OPT_REQUEST);
            } catch(ValidationException $e) {
                abort(400, $e->getMessage());
            }
            $request->request->replace($post);
        }

        $response = $next($request);

        if(is_a($response, RedirectResponse::class)) {
            return $response;
        }

        if($response->exception) {
            return $response;
        }

        $content = $response->getOriginalContent();
        if(is_a($content, 'Illuminate\View\View')) {
            return $response;
        }

        $schema = $this->getSchema($routename, 'RET');
        if(isset($schema)) {
            try {
                $this->validate($content, $schema, self::OPT_RESPONSE);
            } catch(ValidationException $e) {
                abort(500, $e->getMessage());
            }
        }

        return $response;
    }

    public function setSchema(string $name, string $method, array $schema, ?int $mtime): void {
        $this->schemas[$name][$method] = $schema;
        if(is_null($mtime)) {
            return;
        }

        $file = $this->getSchemaCacheName($name, $method);
        if(File::exists($file) && File::lastModified($file) >= $mtime) {
            return;
        }

        try {
            File::put($file, "<?php return " . var_export($schema, true) . ";");
            File::chmod($file, 0664);
        } catch(\Exception $e) {}
    }
    public function getSchema(string $name, string $method):? array {
        $schema = $this->schemas[$name][$method] ?? null;
        if(isset($schema)) {
            return $schema;
        }
        $file = $this->getSchemaCacheName($name, $method);
        if(true !== File::exists($file)) {
            return null;
        }
        $schema = include_once($file);
        return $schema;
    }

    protected function getSchemaCacheName(string $name, string $method): string {
        return $this->cachePath . "{$name}.{$method}.schema.php";
    }

    private function validate($data, array $schema, int $options) {
        $this->validator->validate($data, $schema, $options);
        return $data;
    }
}
