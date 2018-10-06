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
     * Eloquent model that represent Tenant.
     *
     * @var Model
     */
    protected $tenant;

    /**
     * Process request to identify a tenant.
     *
     * @param Request $request
     * @throws InvalidTenantException
     */
    public function process(Request $request)
    {
        try {
            // Identify tenant from current hostname
            $this->findByHostname($request->getHost());
        } catch (InvalidTenantException $exception) {
            $subdomain = array_first(explode('.', $request->getHost()));
            $this->findByIdentifier($subdomain);
        }
    }

    /**
     * Check if exists soma hostname record with current FQDN.
     *
     * @param $fqdn
     * @return TenantManager
     * @throws InvalidTenantException
     */
    public function findByHostname($fqdn)
    {
        $hostname = Hostname::where('fqdn', $fqdn)->first();

        if ($hostname) {
            return $this->loadTenant($hostname->tenant);
        }

        throw new InvalidTenantException("Hostname not founded for current FQDN: ".$fqdn);
    }

    /**
     * Find tenant by identifier.
     *
     * @param $identifier
     * @return TenantManager
     * @throws InvalidTenantException
     */
    public function findByIdentifier($identifier)
    {
        $model = config('multitenancy.tenant.model');
        $instance = (new $model)
            ->newQuery()
            ->where(config('multitenancy.tenant.identifier'), $identifier)
            ->first();

        if($instance) {
            return $this->loadTenant($instance);
        }

        throw new InvalidTenantException("Tenant not founded for identifier: ".$identifier);
    }

    /**
     * Retrieve the current tenant.
     *
     * @return null|Model
     */
    public function tenant() : ?Model
    {
        return $this->tenant;
    }

    /**
     * Load a tenant by id or eloquent model instance.
     *
     * @param $tenant
     * @return $this
     */
    public function loadTenant($tenant)
    {
        if($tenant instanceof Model){
            $this->tenant = $tenant;
        } else {
            $tenant_class = config('multitenancy.tenant.model');
            $this->tenant = $tenant_class::findOrFail($tenant);
        }

        event(new TenantLoaded($this->tenant));

        return $this;
    }

    /**
     * Setup system routes that should belong to a tenant.
     *
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
     */
    public function unique($table, $column = 'NULL') : Unique
    {
        return (new Unique($table, $column))
            ->where(config('multitenancy.tenant.foreign_key'), $this->tenant()->id);
    }

    /**
     * Extends exists validation rule to filter by tenant also.
     *
     * @param string $table
     * @param string $column
     * @return mixed
     */
    public function exists($table, $column = 'NULL') : Exists
    {
        return (new Exists($table, $column))
            ->where(config('multitenancy.tenant.foreign_key'), $this->tenant()->id);
    }
}
