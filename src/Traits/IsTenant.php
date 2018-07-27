<?php

namespace AventureCloud\MultiTenancy\Traits;

use AventureCloud\MultiTenancy\Models\Hostname;
use Illuminate\Database\Eloquent\Relations\HasMany;


trait IsTenant
{
    /**
     * Related hostnames
     *
     * @return HasMany
     */
    public function hostnames() : HasMany
    {
        return $this->hasMany(Hostname::class, config('multitenancy.foreign_key'));
    }
}