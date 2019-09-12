# Notification 
[Laravel](http://laravel.com/) has some pretty sweet functions for sending notification. Due to business nature, certain helper by laravel need to modify. Thus, this package is created to handle the business nature.

This package allows us send push and email notification to all device (iOS, Android, Web) using 3rd party such as [OneSignal](https://github.com/berkayk/laravel-onesignal) and [Firebase](https://firebase.google.com).
## Installation

Install the usual [composer](https://getcomposer.org/) way.

###### Run this command at root directory of your project
```json
"composer require karu/np-notification"
```

#### For Laravel 5.5 and below add provider in config file like below : 
###### app/config/app.php 
```php
	...
	
	'providers' => array(
		...
		Karu\NpNotification\NpNotificationProvider::class,
	],
	
	...

        'aliases' => [
            ...
            NotificationHelper: Karu\NpNotification\Facade\NotificationFacade::class
        ]
```
#

### Configure

Copy the packages config and routes files to respective folder.

```
 php artisan vendor:publish --provider='Karu\NpNotification\NpNotificationProvider'
```

###### app/config/notification.php

```php

<?php

return [
    /*
     * The 3rd party service use to send notification .
     * Supported for now : web -> onesignal  email -> default (Will add in more service in feature)
     */
    'service' => [
        'web'    => 'onesignal',
        'email'  => 'default',
        'mobile' => 'onesignal'
    ],

    /*
     * Array contain template for all the notification.
     */
    'template' => [
        /*
         * Unique template code for notification helper to choose form the view folder.
         */
        '{templateCode}' => [
            'web_push'  => [
                'subject' => '', //subject
                'content' => '' //view location Ex : notification.%s.{templateCode}.pic.email_subject (%s -> country_code)
            ],
            'mobile_push' => [
                'subject' => '', //subject
                'content' => '' //view location Ex : notification.%s.{templateCode}.pic.email_subject (%s -> country_code)
            ],
            'email' => [
                'subject' => '', //subject
                'content' => '' //view location Ex : notification.%s.{templateCode}.pic.email_subject (%s -> country_code)
            ],
            'sms'   => [
                'subject' => '', //subject
                'content' => '' //view location Ex : notification.%s.{templateCode}.pic.email_subject (%s -> country_code)
            ]
        ]
    ],

    /*
     * Method used to get user information.
     * This method must be added to respective modal class
     */
    'user_info_method' => 'getNotificationUserInfo',

    /*
     * Table names
     */
    'tables' => [
        'notification_store' => 'notification',
        'notification_token' => 'notification_token',
    ],

    /*
     * User Type
     */
    'user_type' => [
        'd' => \App\Models\Sample::class, // Sample
        'c' => \App\Models\Sample2::class, // Sample
    ],


    /*
     * Store/Log Notification on database
     */
    'log_notification' => true
];
    
```

###### app/routes/notification.php
```php
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
    Route::put('token/{user_class}/{user_id}', 'NotificationTokenController@update')
        ->name('noti.update-installation');
    Route::put('/{notification_id}', 'NotificationController@update')
        ->name('noti.update');
});

```

## Usage

### Type of Notification Constant 
        NOTIFICATION_TYPE_EMAIL 
        NOTIFICATION_TYPE_WEB_PUSH    
        NOTIFICATION_TYPE_NATIVE_PUSH 
        NOTIFICATION_TYPE_SMS          

### Set Config(Optional) 

```php
 //Default Setting
 $notiSetting = [
    'email' => true,
    'notification' => true, //Web & Mobile
    'sms' => false
 ];

 $noti = NotificationHelper::setConfig($notiSetting)->sendNotificationToUser($user, $templateCode, $extraParam);

```

### Send Notification

```php
 $noti = NotificationHelper::sendNotificationToUser($user, $templateCode, $extraParam);
```

### Get Notification List

```php
    /**
     * @param $userId
     * @param $userType (User class name)
     * @param $notiType (NOTIFICATION_TYPE_EMAIL | NOTIFICATION_TYPE_WEB_PUSH | NOTIFICATION_TYPE_NATIVE_PUSH | NOTIFICATION_TYPE_SMS)
     * @param  int  $pastDay (For all record send 0) 
     *
     * @return collection
     */
 $notiList = NotificationHelper::getUnReadUserNotificationList($userId, $userType, $notiType, $pastDay);
```

### Unsubscribe User from notification

```php
    /**
     * @param $userId
     * @param $userClassType (from notification config user type) 
     * @param $token
     *
     * @return bool
     */
 $notiList = NotificationHelper::unsubscribeUser($userId, $userClassType, $token);
```

## Licence

[View the licence in this repo.](https://github.com/karunais13/np-notification/blob/master/LICENSE)
