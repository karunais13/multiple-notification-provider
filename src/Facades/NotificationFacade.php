<?php
namespace Karu\NpNotification\Facades;

use Illuminate\Support\Facades\Facade;


class NotificationFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Karu\NpNotification\NotificationHelper';
    }
}
