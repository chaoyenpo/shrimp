<?php

namespace App\Http\Middleware;

use Closure;
use Request;
use App\Models\Booth\Services\BoothService;

class BoothMiddleware {
    public function handle($request, Closure $next) {
        /*$service = new BoothService();
        $service->disableBoothOnHourBefore(8);
        $service->closeOrderByHour(3);*/
        BoothService::disableBoothOnHourBefore(8);
        BoothService::closeOrderByHour(3);

        return $next($request);
    }
}