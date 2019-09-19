<?php
declare(strict_types=1);

namespace KentarouTakeda\SafeRouting;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Closure;

class ApplyView
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $routename = Route::currentRouteName();
        if(is_null($routename)) {
            return $response;
        }
        if(isset($response->exception)) {
            return $response;
        }
        if(is_a($response, '\\Illuminate\Http\RedirectResponse')) {
            return $response;
        }
        $content = $response->getOriginalContent();
        if(is_a($content, 'Illuminate\View\View')) {
            return $response;
        }
        if(!is_object($content) && !is_array($content) && !is_null($content)) {
            return $response;
        }

        $response = new Response(view($routename, $content ?? []));
        return $response;
    }
}
