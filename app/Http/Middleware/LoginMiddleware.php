<?php

namespace App\Http\Middleware;

use Closure;
use Request;
use App\Models\Game\Services\GameService;

class LoginMiddleware {
    public function handle($request, Closure $next) {
        if (!session()->get('isLogin'))
        {
            return \App::abort(404);
        }

        return $next($request);
    }
}