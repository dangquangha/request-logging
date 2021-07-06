<?php

namespace Workable\RequestLogging\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;
use App\Libs\Traits\HasHashSlug;

class SearchDailyMiddleware
{
    use HasHashSlug;

    /**
     * @param Request $request
     * @param string $slug1
     * @param string $slug2
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $slugs = $request->route()->parameters();

        $slug_arr = explode('-', $slugs['slug1']);
        $id       = $this->decodeSlug(end($slug_arr));
        if(is_numeric($id)) {
            return $next($request);
        }

        $agent     = new Agent();
        $url       = $request->fullUrl();
        $userAgent = $agent->getUserAgent();
        $ip        = $request->ip();
        $data      = "$url $userAgent $ip";

        Log::channel('search_daily')->info($data);

        return $next($request);
    }
}