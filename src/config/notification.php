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
     * Template view structure by country folder.
     *
     * If is set to TRUE
     * Ex : notification.%s.{templateCode}.email_subject (%s -> country_code)
     *
     * If is set to FALSE
     * Ex : notification.{templateCode}.email_subject
     *
     */
    'template_content_by_country' => true,

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
        'sample' => \App\Models\Sample::class, // Sample
    ],

    'login_user_type' => 'sample',

    /*
     * Store/Log Notification on database
     */
    'log_notification' => true,

    /*
     * Store Notification content on database
     */
    'log_email_content_notification' => true,
    'log_push_content_notification' => true,
];
