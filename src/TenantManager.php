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
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;


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

        if (! $this->hostname) {
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

    /**
     * Extends unique validation rule to filter by tenant also.
     *
     * @param string $table
     * @param string $column
     * @return mixed
     * @throws InvalidTenantException
     */
    public function unique($table, $column = 'NULL') : Unique
    {
        return (new Unique($table, $column))
            ->where('tenant_id', $this->tenant()->id);
    }

    /**
     * Extends exists validation rule to filter by tenant also.
     *
     * @param string $table
     * @param string $column
     * @return mixed
     * @throws InvalidTenantException
     */
    public function exists($table, $column = 'NULL') : Exists
    {
        return (new Exists($table, $column))
            ->where('tenant_id', $this->tenant()->id);
    }
}
