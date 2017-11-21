<?php
namespace AventureCloud\MultiTenancy;

use AventureCloud\MultiTenancy\Exceptions\InvalidTenantException;
use AventureCloud\MultiTenancy\Middleware\LoadTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/**
 * Class TenantManager
 *
 * @package AventureCloud\MultiTenancy
 */
class TenantManager
{
    /**
     * The configuration for the package.
     *
     * @var array
     */
    protected $config;

    /**
     * Eloquent model to represent a Tenant
     *
     * @var Model
     */
    protected $tenant;

    /**
     * TenantManager constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Retrieve the current tenant.
     *
     * @return Model
     */
    public function tenant()
    {
        return $this->tenant;
    }

    /**
     * Load tenant instance from identifier
     * 
     * @param mixed $identifier
     * @return $this
     * @throws InvalidTenantException
     */
    public function setTenant($identifier)
    {
        $instance = (new $this->config['model'])
            ->newQuery()
            ->where($this->config['identifier'], $identifier)
            ->first();

        if (! $instance) {
            throw new InvalidTenantException('Invalid Tenant \''.$identifier.'\'');
        }
        
        $this->tenant = $instance;

        return $this;
    }

    /**
     * Process the request and load the tenant.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws InvalidTenantException
     */
    public function process(Request $request)
    {
        $identifier = $request->route()->parameter('_multitenant_');

        $this->setTenant($identifier);

        $request->route()->forgetParameter('_multitenant_');
    }

    /**
     * Setup system routes that should belong to a tenant.
     *
     * @param \Closure $routes
     *
     * @return mixed
     */
    public function routes(\Closure $routes)
    {
        Route::pattern('_multitenant_', '[a-z0-9.]+');

        return Route::group(
            [
                'domain'        => '{_multitenant_}',
                'middleware'    => LoadTenant::class
            ],
            $routes);
    }

    /**
     * Compose route of the given path for the current tenant.
     *
     * @param       $name
     * @param array $parameters
     * @param bool  $absolute
     *
     * @return string
     */
    public function route($name, $parameters = [], $absolute = true)
    {
        return route($name, array_merge([$this->config['identifier']], $parameters), $absolute);
    }
}
