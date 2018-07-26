<?php

namespace AventureCloud\MultiTenancy\Traits;


use AventureCloud\MultiTenancy\Models\Hostname;

trait IsTenant
{
    public function hostnames()
    {
        return $this->hasMany(Hostname::class, config('multitenancy.foreign_key'));
    }
}