# Laravel Multi Tenancy

[![Latest Stable Version](https://poser.pugx.org/aventure-cloud/laravel-tenancy/v/stable)](https://packagist.org/packages/aventure-cloud/laravel-tenancy)
[![Total Downloads](https://poser.pugx.org/aventure-cloud/laravel-tenancy/downloads)](https://packagist.org/packages/aventure-cloud/laravel-tenancy)
[![License](https://poser.pugx.org/aventure-cloud/laravel-tenancy/license)](https://packagist.org/packages/aventure-cloud/laravel-tenancy)


Single database Multi-Tenancy solution for Laravel applications.

- **Author:** Valerio Barbera - [valerio@aventuresrl.com](mailto:valerio@aventuresrl.com)
- **Author Website:** [www.aventuresrl.com](target="_blank":https://www.aventuresrl.com)


# Installation
`composer require aventure-cloud/laravel-tenancy`

After installation you don't need to add `MultiTenancyServiceProvider` 
in your configuration because it's auto-discovered from Laravel.


## Configuration
To get full control of behavior of the package you need publish the `config/multitenancy.php` file.

`php artisan vendor:publish --provider="AventureCloud\MultiTenancy\MultiTenancyServiceProvider"`


```php

    // The foreign key for identifying tenant ownership
    // in all application models
    'foreign_key' => 'company_id',

    // Fields used to identify a tenant
    'identifiers' => [
        'primary' => 'slug',
        'secondary' => 'domain',
    ],

    // The domain used for subdomain lookup,
    // tenant could be {slug}.mydomain.com
    'domain' => env('MULTITENANCY_DOMAIN', 'mydomain.com'),

    // The model representing a tenant
    'model' => \App\Tenant::class
    
```


## Eloquent Model Trait
Attach `BelongsToTenant` trait to models that you want scope by tenant:

```php
class Post extends Model 
{
    use BelongsToTenant;
    
    ...
}
```


## Tenant Routing
You need to wrap all routes with tenant dependency before applying any other middleware.
You can use the routes method by our facade that handle tenant recognition process automatically for you. 
```php
protected function mapWebRoutes()
{
    Tenancy::routes(function (Router $router) {
    
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
            
    });
}
```


##Generate links for tenant
To generate a url for a tenant based route, you can use the following method:
```php
Tenancy::route($name, $paramaters = [], $absolute = false);
```
`url()` methods is not currently available because it isn't working exactly as intended.


## LICENSE
This package are licensed under the [MIT](LICENSE) license.
