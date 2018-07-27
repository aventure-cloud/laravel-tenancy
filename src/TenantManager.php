<?php
namespace AventureCloud\MultiTenancy;

use AventureCloud\MultiTenancy\Events\TenantLoaded;
use AventureCloud\MultiTenancy\Exceptions\InvalidTenantException;
use AventureCloud\MultiTenancy\Middleware\LoadTenant;
use AventureCloud\MultiTenancy\Models\Hostname;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;


class TenantManager
{
    /**
     * Eloquent model that represent current Hostname
     *
     * @var Model
     */
    protected $hostname;

    /**
     * Eloquent model that represent Tenant
     *
     * @var Model
     */
    protected $tenant;

    /**
     * Get Hostname
     *
     * @param string|null $fqdn
     * @return Model
     * @throws InvalidTenantException
     */
    public function hostname(string $fqdn = null)
    {
        return $fqdn !== null
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
        $this->hostname = Hostname::where('fqdn', $fqdn)->first();

        if (! $this->hostname->exists) {
            throw new InvalidTenantException("Hostname not founded for current FQDN: ".$fqdn);
        }

        event(new TenantLoaded($this->tenant = $this->hostname->tenant));

        return $this->hostname;
    }

    /**
     * Retrieve the current tenant.
     *
     * @param string|null $fqdn
     * @return Model
     * @throws InvalidTenantException
     */
    public function tenant() : Model
    {
        return $this->tenant;
    }

    /**
     * Setup system routes that should belong to a tenant.
     *
     * @param \Closure $routes
     * @return mixed
     */
    public function routes()
    {
        return Route::domain('{tenant}')->middleware(LoadTenant::class);
    }
}
