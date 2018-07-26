<?php
namespace AventureCloud\MultiTenancy;

use AventureCloud\MultiTenancy\Events\TenantLoaded;
use AventureCloud\MultiTenancy\Exceptions\InvalidTenantException;
use AventureCloud\MultiTenancy\Middleware\LoadTenant;
use AventureCloud\MultiTenancy\Models\Hostname;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;


class TenantManager
{
    /**
     * Eloquent model to represent a Tenant
     *
     * @var Model
     */
    protected $hostname;

    /**
     * Get Hostname
     *
     * @param string|null $fqdn
     * @throws InvalidTenantException
     */
    public function hostname(string $fqdn = null)
    {
        $fqdn !== null
            ? $this->identifyHostname($fqdn)
            : $this->hostname;
    }

    /**
     * Check if exists soma hostname record with current FQDN
     *
     * @param $fqdn
     * @return Hostname
     * @throws InvalidTenantException
     */
    public function identifyHostname($fqdn) : Hostname
    {
        $model = Hostname::where('fqdn', $fqdn)->first();

        if($model){
            $this->hostname = $model;
        }

        if (! $model) {
            throw new InvalidTenantException("Hostname not founded for current FQDN: ".$fqdn);
        }

        event(new TenantLoaded($this->hostname->tenant));

        return $this->hostname;
    }

    /**
     * Retrieve the current tenant.
     *
     * @param string|null $fqdn
     * @return Model
     * @throws InvalidTenantException
     */
    public function tenant(string $fqdn = null) : Model
    {
        if($fqdn !== null){
            $this->identifyHostname($fqdn);
        }

        return $this->hostname->tenant;
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
        return Route::domain('{tenant}')
            ->middleware(LoadTenant::class)
            ->group($routes);
    }
}
