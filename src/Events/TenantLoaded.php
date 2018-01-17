<?php

namespace AventureCloud\MultiTenancy\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TenantLoaded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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
