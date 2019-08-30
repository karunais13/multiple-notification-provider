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
