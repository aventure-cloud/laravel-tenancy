<?php

namespace AventureCloud\MultiTenancy\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Tenancy
 *
 * @package AventureCloud\MultiTenancy\Facades
 */
class Tenancy extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'tenancy';
    }
}
