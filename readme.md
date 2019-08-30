<img src="https://websso.nipponpaint.com.my/img/logo/nippon-logo.png"></p>
# Nippon Paint Notification 
[Laravel](http://laravel.com/) has some pretty sweet functions for sending notification. Due to business nature, certain helper by laravel need to modify. Thus, this package is created to handle the business nature.

This package allows us send push and email notification to all device (iOS, Android, Web) using 3rd party such as [OneSignal](https://onesignal.com/) and [Firebase](https://firebase.google.com).

### OneSignal(Optional)
###### Add in this package if one of your 3rd party provider is [OneSignal](https://onesignal.com/)
https://github.com/berkayk/laravel-onesignal (Recommended)

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

Copy the packages config files.

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
        'web'   => 'onesignal',
        'email' => 'default'
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
];


    
```

## Usage

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

## Licence

[View the licence in this repo.](https://github.com/karunais13/np-notification/blob/master/LICENSE)
