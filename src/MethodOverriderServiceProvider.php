<?php

namespace Athwari\MethodOverrider;

use Illuminate\Support\ServiceProvider;
use Athwari\MethodOverrider\Proxy\ProxyFactory;

class MethodOverriderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/method-overrider.php',
            'method-overrider'
        );

        $this->app->singleton(ProxyFactory::class);
        $this->app->singleton(MethodOverrider::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/method-overrider.php' => config_path('method-overrider.php'),
        ], 'method-overrider-config');
    }
}
