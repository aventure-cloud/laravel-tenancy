# Laravel Multi Tenancy

[![Latest Stable Version](https://poser.pugx.org/aventure-cloud/laravel-tenancy/v/stable)](https://packagist.org/packages/aventure-cloud/laravel-tenancy)
[![Total Downloads](https://poser.pugx.org/aventure-cloud/laravel-tenancy/downloads)](https://packagist.org/packages/aventure-cloud/laravel-tenancy)
[![License](https://poser.pugx.org/aventure-cloud/laravel-tenancy/license)](https://packagist.org/packages/aventure-cloud/laravel-tenancy)


Single database Multi-Tenancy solution for Laravel applications.

- **Author:** Valerio Barbera - [valerio@aventuresrl.com](mailto:valerio@aventuresrl.com)
- **Author Website:** [www.phpexpert.it](target="_blank":https://www.phpexpert.it)


# Installation
`composer require aventure-cloud/laravel-tenancy`

After installation you don't need to add `MultiTenancyServiceProvider` 
in your configuration because it's auto-discovered from Laravel.


## Configuration
To get full control of the package's behavior you need publish `config/multitenancy.php` file.

`php artisan vendor:publish --provider="AventureCloud\MultiTenancy\MultiTenancyServiceProvider"`


```php

    'tenant' => [
        // The model representing a tenant
        'model' => App\Tenant::class,

        // The foreign key for identifying tenant ownership
        // in all application models
        'foreign_key' => env('MULTITENANCY_FOREIGN_KEY', 'company_id'),
    ],

    // Field used to identify a tenant in the url
    'hostname' => [
        'default' => env('MULTITENANCY_HOSTNAME_DEFAULT', 'www.mydomain.com')
    ]

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


## Load a Tenant
There are two ways to load a tenant instance. Once a tenant is loaded 
all subsequent query will be scoped.


## Configuring multi-tenant routes
You need to wrap all routes with tenant dependency before applying any other middleware.
You can use the routes method by our facade that handle tenant recognition process automatically for you. 

```php
protected function mapTenantRoutes()
{
    // Wrap tenant routes here before every others middleware
    Tenancy::routes()->group(function () {
    
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/tenant/web.php'));
            
    });
}
```


## Default routes
Your landing page need to be loaded under your personal url, so you need to tell to the laravel how to identify 
your general routes. In your `RoutesServiceProvider` you can modify your `map` method like:

```php
protected function map()
{

    Route::domain(config('multitenancy.hostname.default')->group(function() {
		$this->mapApiRoutes();
        $this->mapWebRoutes();
    });
	
}
```


## Validation Rules
Scoping your application by tenant can cause wrong behavior of the `unique` and `exists` validation rules
that performs a query on the database without considering tenant scope by default.

This package ships with extended version of these two rules that run a query filtered by tenant.

```php
public function store(Request $request)
{
    $request->validate([
        'email' => [Tenancy::unique('users', 'email')],
        'role_id' => [Tenancy::exists('roles', 'id')]
    ])
}
```



## Events
When a tenant is founded and stored in Tenancy service the package fire an event with attacched tenant instance:
- TenantLoaded


## LICENSE
This package are licensed under the [MIT](LICENSE) license.
