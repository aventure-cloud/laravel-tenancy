<?php
namespace AventureCloud\MultiTenancy;

use Illuminate\Support\ServiceProvider;

/**
 * Class MultiTenancyServiceProvider
 *
 * @package AventureCloud\MultiTenancy
 */
class MultiTenancyServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/multitenancy.php' => config_path('multitenancy.php')
        ], 'config');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //Bind service in IoC container
        $this->app->singleton('tenancy', function(){
            return new TenantManager();
        });
    }
}
