<?php
namespace AventureCloud\MultiTenancy\Middleware;

use AventureCloud\MultiTenancy\Facades\Tenancy;
use Closure;
use Illuminate\Support\Facades\URL;

/**
 * Class LoadTenant
 *
 * @package AventureCloud\MultiTenancy\Middleware
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
        // Forgot tenant parameter to avoid injecting parameter into each controller
        $request->route()->forgetParameter('tenant');

        // Identify tenant from current hostname
        Tenancy::hostname($request->getHost());

        // Set default value for {tenant} route parameter to avoid specifing it
        // using route() function to generate url
        URL::defaults(['tenant' => $request->getHost()]);

        return $next($request);
    }
}