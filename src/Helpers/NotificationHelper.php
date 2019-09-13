<?php

namespace Karu\NpNotification\Helpers;

use Karu\NpNotification\Sender;

//Models
use Karu\NpNotification\Models\Notification;
use Karu\NpNotification\Models\NotificationToken;

//Facades
use DB;

class NotificationHelper extends Sender
{
    /**
     * @var User Instance
     */
    public $rcver;

    /**
     * @var array
     */
    protected $response = [
        'notification_web'      => [false],
        'notification_mobile'   => [false],
        'email'                 => [false]
    ];

    /**
     *
     * Override Default Config
     *
     * @param  array  $param
     *
     * @return $this
     */
    public function setConfig(Array $param)
    {
        if( is_array($param) ){
            foreach( $param as $key => $value ){
                switch($key){
                    case 'email':
                        $this->email = $value;
                        break;
                    case 'notification' :
                        $this->notificationWeb = $value;
                        $this->notificationMobile = $value;
                        break;
                    case 'sms' :
                        $this->sms = $value;
                        break;
                }
            }
        }

        return $this;
    }

    /**
     * @param $user User instance
     * @param $templateCode Code that defined in the config file
     * @param array $extraParam
     *
     * @return array
     * @throws \Exception
     */
    public function sendNotificationToUser( $user, $templateCode, $extraParam=[] )
    {
        $content = $this->getTemplate($templateCode);
        if( !$content )
            throw new \Exception('Template Code dosen\'t exist.');

        if( !is_array($user) )
            $user = [$user];

        foreach( $user as $rcver ){

            $this->rcver = $rcver;

            $userInfo = $this->getUserInformation($rcver);
            if( !$userInfo )
                continue;

            $data = array_merge($userInfo, $extraParam);

            DB::beginTransaction();

            $this->sendEmail($userInfo,  $content['email'], $data);

            $this->sendNotificationWeb($userInfo, $content['web_push'], $data);

            $this->sendNotificationMobile($userInfo, $content['mobile_push'], $data);

            DB::commit();
        }

        return $this->response;
    }

    /**
     * @param $userId User ID
     * @param $userType User Type that define in config file
     * @param  int  $notiType Notification Type Constant
     * @param  int  $passDay Days
     *
     * @return Notification instance
     */
    public function getUnReadUserNotificationList( $userId, $userType, $notiType = NOTIFICATION_TYPE_WEB_PUSH, $passDay = 1)
    {
        return (new Notification)->getUnReadUserNotificationList($userId, $userType, $notiType, $passDay);
    }

    /**
     * @param $userId User ID
     * @param $userType User Type that define in config file
     * @param $token Notification Token
     *
     * @return bool
     */
    public function unsubscribeUser($userId, $userType, $token)
    {
        $notification = (new NotificationToken)->unsubscribeUser($userId, $userType, $token);

        return true;
    }
}
