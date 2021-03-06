<?php

namespace Workable\RequestLogging\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;
use Workable\RequestLogging\Libs\Common;

class SearchDailyMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $agent     = new Agent();
        $url       = $request->fullUrl();
        $userAgent = $agent->getUserAgent();
        $ip        = Common::get_client_ip();
        $data      = "$url $userAgent $ip";

        if (!$agent->isRobot() && !$request->ajax()) {
            Log::channel('search_daily')->emergency($data);
        }

        return $next($request);
    }
}
