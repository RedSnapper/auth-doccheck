<?php

namespace RedSnapper\DocCheck;

use Illuminate\Support\ServiceProvider;

class DocCheckServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('doccheck.php'),
            ], 'doccheck');

        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'doccheck');

        // Register the main class to use with the facade
        $this->app->bind(DocCheckProvider::class, function ($app) {
            $provider =  new DocCheckProvider(config('doccheck.client_key'),config('client.secret'));
            $provider->setRequest($app['request']);
            return $provider;
        });
    }
}
