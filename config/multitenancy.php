<?php

return [

    // The foreign key for identifying tenant ownership
    // in all application models
    'foreign_key' => 'company_id',

    // Fields used to identify a tenant
    'indentifiers' => [
        'primary' => 'slug',
        'secondary' => 'domain',
    ],

    // The domain used for subdomain lookup,
    // tenant could be {slug}.mydomain.com
    'domain' => env('MULTITENANCY_DOMAIN', 'mydomain.com'),

    // The model representing a tenant
    'model' => \App\Tenant::class

];