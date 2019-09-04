<?php
/*
|--------------------------------------------------------------------------
| Notification
|--------------------------------------------------------------------------
|
*/
Route::group(['prefix'=> 'notification'], function(){
    /*
    |
    | User Class -> set in the notification config with type as key
    |
    */
    Route::put('token/{user_class}/{user_id}', 'Karu\NpNotification\Controller\NotificationTokenController@update')
        ->name('noti.update-installation');
    Route::put('/{notification_id}', 'Karu\NpNotification\Controller\NotificationController@update')
        ->name('noti.update');
});
