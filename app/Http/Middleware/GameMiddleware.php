<?php

namespace App\Http\Middleware;

use Closure;
use Request;
use App\Models\Game\Services\GameService;

class GameMiddleware {
    public function handle($request, Closure $next) {
    	//整備時間自動
        GameService::updateSignup(30);
        // GameService::updatePending();
        // GameService::updatePrepare(1);
        // GameService::handlePrepare();
        GameService::updateIng();

        return $next($request);
    }
}