<?php

namespace AventureCloud\MultiTenancy\Contracts;

interface Hostname
{
    public function tenant();
}