<?php

namespace AventureCloud\MultiTenancy\Events;

class TenantLoaded
{
    /**
     * @var
     */
    public $tenant;

    /**
     * Create a new event instance.
     *
     * @param $tenant
     */
    public function __construct($tenant)
    {
        $this->tenant = $tenant;
    }
}
