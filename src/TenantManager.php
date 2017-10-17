<?php
namespace AventureCloud\MultiTenancy;

use AventureCloud\MultiTenancy\Exceptions\InvalidTenantException;
use AventureCloud\MultiTenancy\Middleware\LoadTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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
     */
    public function __construct()
    {
        $this->config = config('multitenancy');
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
     * Process the request and load the tenant.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws InvalidTenantException
     */
    public function process(Request $request)
    {
        $identifier = $request->route()->parameter('_multitenant_');
        $primary = false;

        if (strpos($identifier, $this->config['domain']) !== false) {
            $identifier = str_replace('.'.$this->config['domain'], '', $identifier);
            $primary = true;
        }

        $this->tenant = (new $this->config['model'])
            ->newQuery()
            ->where($primary ? $this->config['identifiers']['primary'] : $this->config['identifiers']['secondary'], '=', $identifier)
            ->first();

        if (! $this->tenant) {
            throw new InvalidTenantException('Invalid Tenant \''.$identifier.'\'');
        }

        $request->route()->forgetParameter('_multitenant_');
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
        return route($name, array_merge([$this->getIdentifier()], $parameters), $absolute);
    }

    /**
     * Get identifier to retrieve tenant in the subsequent request
     *
     * @return string
     */
    protected function getIdentifier()
    {
        if ($secondary = $this->tenant->{$this->config['identifiers']['secondary']}) {
            return $secondary;
        } else {
            return $this->tenant->{$this->config['identifiers']['primary']}.'.'.$this->config['domain'];
        }
    }
}