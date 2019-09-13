<?php


namespace Karu\NpNotification\Traits;


trait Validation
{
    public $email = true;
    public $notificationWeb = true; // web notification
    public $notificationMobile = true; // native notification
    public $sms = false; // sms

    public function isNotificationWeb(): bool
    {
        return $this->notificationWeb;
    }

    public function isNotificationMobile(): bool
    {
        return $this->notificationMobile;
    }

    public function isEmail(): bool
    {
        return $this->email;
    }

    public function isSms(): bool
    {
        return $this->sms;
    }
}
