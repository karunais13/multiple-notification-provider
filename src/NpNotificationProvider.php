<?php

namespace Karu\NpNotification;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

class NpNotificationProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if (!defined('NOTIFICATION_TYPE_EMAIL')) {
            define("NOTIFICATION_TYPE_EMAIL", 1);
        }
        if (!defined('NOTIFICATION_TYPE_WEB_PUSH')) {
            define("NOTIFICATION_TYPE_WEB_PUSH", 2);
        }
        if (!defined('NOTIFICATION_TYPE_NATIVE_PUSH')) {
            define("NOTIFICATION_TYPE_NATIVE_PUSH", 3);
        }
        if (!defined('NOTIFICATION_TYPE_SMS')) {
            define("NOTIFICATION_TYPE_SMS", 4);
        }

        if (!defined('NOTIFICATION_TOKEN_TYPE_WEB')) {
            define("NOTIFICATION_TOKEN_TYPE_WEB", 1);
        }
        if (!defined('NOTIFICATION_TOKEN_TYPE_IOS')) {
            define("NOTIFICATION_TOKEN_TYPE_IOS", 2);
        }
        if (!defined('NOTIFICATION_TOKEN_TYPE_ANDROID')) {
            define("NOTIFICATION_TOKEN_TYPE_ANDROID", 3);
        }
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
        return strpos($this->app->version(), 'Lumen') !== false;
    }
}
