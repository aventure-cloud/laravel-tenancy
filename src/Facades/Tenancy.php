<?php

namespace AventureCloud\MultiTenancy\Facades;

use AventureCloud\MultiTenancy\TenantManager;
use Illuminate\Support\Facades\Facade;

class Tenancy extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'tenancy';
    }
}
