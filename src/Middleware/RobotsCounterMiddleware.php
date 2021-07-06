<?php

namespace Workable\RequestLogging\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;
use const LARAVEL_START_EXECUTION_TIME_2;

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
//            $botAgent = $agent->robot();
            $url       = $request->fullUrl();
            $ip        = $request->ip();
            $processTime = (int)((microtime(true) - LARAVEL_START_EXECUTION_TIME_2) * 1000) . 'ms';
            $data = "$botAgent $url $ip $processTime";

            Log::channel('robot_counter')
                ->emergency($data);
        }
        return $response;
    }
}
