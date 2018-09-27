<?php

namespace AventureCloud\MultiTenancy\Queue;

use AventureCloud\MultiTenancy\Facades\Tenancy;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

trait TenantAwareJob
{
    use SerializesModels {
        __sleep as serializedSleep;
        __wakeup as serializedWakeup;
    }

    /**
     * The ID of the tenant to be used.
     *
     * @var int
     */
    protected $tenant_id;

    public function __sleep()
    {
        // If tenant was not override from "onTenant" method and exists a current tenant
        if (!$this->tenant_id && $tenant = Tenancy::tenant()) {
            $this->tenant_id = $tenant->getKey();
        }

        $attributes = $this->serializedSleep();
        return $attributes;
    }

    public function __wakeup()
    {
        if (isset($this->tenant_id)) {
            Tenancy::loadTenant($this->tenant_id);
        }
        $this->serializedWakeup();
    }

    /**
     * Manually override the tenant to be used on the queue.
     *
     * @param $tenant
     * @return $this
     */
    public function onTenant($tenant)
    {
        $this->tenant_id = ($tenant instanceof Model)
            ? $tenant->getKey()
            : $tenant;

        return $this;
    }
}