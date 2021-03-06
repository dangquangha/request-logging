<?php

namespace Workable\RequestLogging\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;
use Workable\RequestLogging\Libs\Common;
use const LARAVEL_START_EXECUTION_TIME;

class RobotsCounterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $agent = new Agent();

        if ($agent->isRobot()  && !$request->ajax()) {
            if ( !in_array($request->method(), config('robots_counter.accepted_methods')) )
                return $response;

            $botAgent = $agent->getUserAgent();
            $url       = $request->fullUrl();
            $ip        = Common::get_client_ip();
            $processTime = (int)((microtime(true) - LARAVEL_START_EXECUTION_TIME) * 1000) . 'ms';

            $data = "$botAgent $url $ip $processTime";

            Log::channel('robot_counter')
                ->emergency($data);
        }
        return $response;
    }
}
