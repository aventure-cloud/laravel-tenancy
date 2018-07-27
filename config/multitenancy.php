<?php

return [

    'tenant' => [
        // The model representing a tenant
        'model' => App\Tenant::class,

        // The foreign key for identifying tenant ownership
        // in all application models
        'foreign_key' => env('MULTITENANCY_FOREIGN_KEY', 'company_id'),
    ],

    // Field used to identify a tenant in the url
    'hostname' => [
        'default' => env('MULTITENANCY_FQDN_DEFAULT', 'www.mydomain.com')
    ],


];