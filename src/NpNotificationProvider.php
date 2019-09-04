<?php

namespace Karu\NpNotification;

use Illuminate\Support\ServiceProvider;

class NpNotificationProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');

        $this->publishes([
            __DIR__.'/config/notification.php' => config_path('notification.php'),
        ]);

        $this->publishes([
            __DIR__.'/routes/notification.php' => base_path('routes/notification.php'),
        ]);

        $this->loadRoutesFrom(base_path('routes/notification.php'));
    }
}
