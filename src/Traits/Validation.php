<?php


namespace Karu\NpNotification\Traits;


trait Validation
{
    public $email = true;
    public $notificationWeb = true; // web notification
    public $notificationMobile = true; // native notification
    public $sms = false; // sms

    public function resetConfig()
    {
        $this->email = true;
        $this->notificationMobile = true;
        $this->notificationWeb = true;
        $this->sms = false;
    }

    public function updateConfig($param=[])
    {
        $this->resetConfig();

        if( is_array($param) ){
            foreach( $param as $key => $value ){
                switch($key){
                    case 'email':
                        $this->email = $value;
                        break;
                    case 'mobile' :
                        $this->notificationMobile = $value;
                        break;
                    case 'web' :
                        $this->notificationWeb = $value;
                        break;
                    case 'sms' :
                        $this->sms = $value;
                        break;
                }
            }
        }
    }

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
