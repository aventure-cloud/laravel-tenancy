<?php

namespace AventureCloud\MultiTenancy\Traits;

use AventureCloud\MultiTenancy\Facades\Tenancy;
use AventureCloud\MultiTenancy\Scopes\TenantOwnedScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


trait BelongsToTenant
{
    /**
     * Boot trait
     */
    public static function bootBelongsToTenant()
    {
        // To filter in select
        static::addGlobalScope(new TenantOwnedScope());

        // Apply tenant ownership when creating
        static::creating(function ($model){
            if(Tenancy::tenant()) {
                $model->{config('multitenancy.tenant.foreign_key')} = Tenancy::tenant()->id;
            }
        });
    }

    /**
     * Get current tenant
     *
     * @return BelongsTo
     */
    public function tenant() : BelongsTo
    {
        return $this->belongsTo(config('multitenancy.tenant.model'), config('multitenancy.tenant.foreign_key'));
    }
}