<?php

namespace Workable\RequestLogging\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;
use Workable\RequestLogging\Libs\Common;

class RefererLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $agent     = new Agent();
        $url       = $request->fullUrl();
        $userAgent = $agent->getUserAgent();
        $ip        = Common::get_client_ip();
        $referer   = $request->header()['referer'][0] ?? null;
        $data      = "$referer $url $userAgent $ip";

        if (preg_match('/google\.com/', $referer) || preg_match('/coccoc\.com/', $referer)) {
            Log::channel('refer')->emergency($data);
        }

        return $next($request);
    }
}
