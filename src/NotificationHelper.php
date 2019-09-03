<?php

namespace Karu\NpNotification;

use Karu\NpNotification\Models\Notification;

use Carbon\Carbon;
use DB;
use View;

class NotificationHelper
{
    private $notiweb;
    private $notiemail;
    private $rcver;
    protected $email = true;
    protected $notificationWeb = true; // web & native notification
    protected $notificationMobile = true; // web & native notification
    protected $sms = false; // sms
    protected $response = [
        'notification_web'  => false,
        'email'             => false
    ];

    public function __construct()
    {
        switch( config('notification.service.web') ){
            case 'onesignal':
                $this->notiweb		= new OneSignalHelper();
                break;
        }

        switch( config('notification.service.email') ){
            case 'default':
                $this->notiemail    = new EmailHelper();
                break;
        }
    }

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

    public function sendNotificationToUser( $user, $templateCode, $extraParam=[] )
    {
        $content = $this->getTemplate($templateCode);
        if( !$content )
            return $this->response;

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

            DB::commit();
        }

        return $this->response;
    }

    private function isNotificationWeb(): bool
    {
        return $this->notificationWeb;
    }

    private function isNotificationMobile(): bool
    {
        return $this->notificationMobile;
    }

    private  function isEmail(): bool
    {
        return $this->email;
    }

    private  function isSms(): bool
    {
        return $this->sms;
    }

    private function getUserInformation($rcver)
    {
        if( is_object($rcver) && method_exists($rcver, config('notification.user_info_method')) ){
            return $rcver->{config('notification.user_info_method')};
        }

        return null;
    }

    private function getMessageObject($type, $data)
    {
        switch($type){
            case 'email':
                return [
                    'subject' => [
                        'view' => sprintf($data['template']['subject'], strtolower($data['country_code'])),
                        'data' => $data
                    ],
                    'content' => [
                        'view' => sprintf($data['template']['content'], strtolower($data['country_code'])),
                        'data' => $data
                    ],
                ];
                break;
            case 'webnoti' :
                $content = $data['template']['content'] ? view(sprintf($data['template']['content'], strtolower($data['country_code'])), $data)->render() : '';
                return [
                    'msg' => $content,
                    'url' => $data['url'] ?? null,
                ];
                break;
            default :
                return [];
        }
    }

    private function getTemplate($templateCode)
    {
        return config("notification.template.{$templateCode}");
    }

    private function renderContent($view, $data)
    {
        return view($view, $data)->render();
    }

    private function renderSubject($view, $data)
    {
        return view($view, $data)->render();
    }

    private function sendEmail($user, $views, $data)
    {
        if( $this->isEmail() ){
            $data['template'] = $views;

            $msg = $this->getMessageObject('email', $data);

            $this->response['email'] = $this->notiemail->sendNotificationToUser($user, $msg);

            $html  = View::make($msg['content']['view'], $msg['content']['data'])->render();
            $sub  = View::make($msg['subject']['view'], $msg['subject']['data'])->render();
            $content = [
                'content' => $html,
                'subject' => $sub,
                'target'  => $data['url'] ?? null
            ];
            $this->addToDatabase($this->rcver, Notification::NOTIFICATION_TYPE_EMAIL, $content);
        }
    }

    private function sendNotificationWeb($user, $views, $data)
    {
        if( $this->isNotificationWeb() ){
            $data['template'] = $views;

            $msg = $this->getMessageObject('webnoti', $data);

            $this->response['notification_web'] = $this->notiweb->sendNotificationToUser($user, $this->getMessageObject('webnoti', $data));

            $content = [
                'content' => $msg['msg'],
                'subject' => $msg['msg'],
                'target'  => $msg['url'] ?? null
            ];
            $this->addToDatabase($this->rcver, Notification::NOTIFICATION_TYPE_WEB_PUSH, $content);
        }
    }

    private function sendNotificationMobile($user, $views, $data)
    {
        if( $this->isNotificationMobile() ){
            $data['template'] = $views;

            $msg = $this->getMessageObject('webnoti', $data);

            $this->response['notification_mobile'] = $this->notiweb->sendNotificationToUser($user, $this->getMessageObject('webnoti', $data));

            $content = [
                'content' => $msg['msg'],
                'subject' => $msg['msg'],
                'target'  => $msg['url'] ?? null
            ];
            $this->addToDatabase($this->rcver, Notification::NOTIFICATION_TYPE_NATIVE_PUSH, $content);
        }
    }


    private function addToDatabase($user, $type, $content)
    {
        if( config('notification.log_notification') ){
            $data = [
                'emp_code'  => $user->emp_code,
                'type'      => $type,
                'content'   => $content['content'],
                'target'    => $content['target'],
                'subject'   => $content['subject'],
                'is_read'   => false,
                'created_at'   => Carbon::now(),
            ];

            return Notification::insert($data);
        }
    }
}
