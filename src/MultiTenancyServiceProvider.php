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
        //
    }
}
