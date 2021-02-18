<?php

namespace Karu\NpNotification;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Lumen\Application;
use Route;

class NpNotificationProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        define("NOTIFICATION_TYPE_EMAIL", 1);
        define("NOTIFICATION_TYPE_WEB_PUSH", 2);
        define("NOTIFICATION_TYPE_NATIVE_PUSH", 3);
        define("NOTIFICATION_TYPE_SMS", 4);

        define("NOTIFICATION_TOKEN_TYPE_WEB", 1);
        define("NOTIFICATION_TOKEN_TYPE_IOS", 2);
        define("NOTIFICATION_TOKEN_TYPE_ANDROID", 3);
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

        if( !$this->isLumen() ){
            Route::middleware('web')
                ->group(__DIR__.'/routes/notification.php');

            Route::prefix('api')
                ->middleware('api')
                ->group(__DIR__.'/routes/notification.php');
        }
    }

    /**
     * Check if app uses Lumen.
     *
     * @return bool
     */
    protected function isLumen()
    {
        return $this->app instanceof Application ?? false;
    }
}
