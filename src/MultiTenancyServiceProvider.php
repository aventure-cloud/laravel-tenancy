<?php
namespace AventureCloud\MultiTenancy;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

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

        Route::pattern('tenant', '[a-z0-9.\-]+');
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
