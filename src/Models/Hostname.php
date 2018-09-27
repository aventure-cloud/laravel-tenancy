<?php

namespace AventureCloud\MultiTenancy\Models;

use AventureCloud\MultiTenancy\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \AventureCloud\MultiTenancy\Contracts\Hostname as HostnameContract;

class Hostname extends Model implements HostnameContract
{
    use BelongsToTenant, SoftDeletes;
}