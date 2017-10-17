<?php

namespace AventureCloud\MultiTenancy\Traits;

use AventureCloud\MultiTenancy\Facades\Tenancy;
use AventureCloud\MultiTenancy\Scopes\TenantOwnedScope;

/**
 * Trait BelongsToTenant
 *
 * @package AventureCloud\MultiTenancy\Traits
 */
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
            if(Tenancy::tenant())
                $model->{config('multitenancy.foreign_key')} = Tenancy::tenant()->id;
        });
    }
}