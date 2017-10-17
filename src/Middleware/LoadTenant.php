<?php
namespace AventureCloud\MultiTenancy\Middleware;

use AventureCloud\MultiTenancy\Facades\Tenancy;
use Closure;

/**
 * Class LoadTenant
 *
 * @package Ollieslab\Multitenancy\Middleware
 */
class LoadTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Tenancy::process($request);

        return $next($request);
    }
}