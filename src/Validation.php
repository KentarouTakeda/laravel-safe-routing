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
    private const OPT_GET = C::CHECK_MODE_TYPE_CAST | C::CHECK_MODE_EXCEPTIONS | C::CHECK_MODE_COERCE_TYPES | C::CHECK_MODE_APPLY_DEFAULTS;
    private const OPT_POST = C::CHECK_MODE_TYPE_CAST | C::CHECK_MODE_EXCEPTIONS | C::CHECK_MODE_COERCE_TYPES | C::CHECK_MODE_APPLY_DEFAULTS;
    private const OPT_RESPONSE = C::CHECK_MODE_TYPE_CAST | C::CHECK_MODE_EXCEPTIONS;

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
                $query = $this->validate($request->query(), $schema, self::OPT_GET);
            } catch(ValidationException $e) {
                abort(400, $e->getMessage());
            }
            $request->query->replace($query);
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
