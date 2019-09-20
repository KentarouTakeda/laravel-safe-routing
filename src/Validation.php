<?php
declare(strict_types=1);

namespace KentarouTakeda\SafeRouting;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    public function __construct(Validator $validator) {
        $this->validator = $validator;
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
        return $response;
    }

    public function setSchema(string $name, string $method, array $schema): void {
        $this->schemas[$name][$method] = $schema;
    }
    public function getSchema(string $name, string $method):? array {
        return $this->schemas[$name][$method] ?? null;
    }

    private function validate($data, array $schema, int $options) {
        $this->validator->validate($data, $schema, $options);
        return $data;
    }
}
