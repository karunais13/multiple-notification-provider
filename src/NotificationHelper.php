<?php

namespace Karu\NpNotification;

use App\Models\ClientContact;
use App\Models\DepotSalesrep;
use App\Models\Notification;
use App\Models\NotificationToken;
use App\Models\TbUserInstallation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Exception;

use DB;
use Mail;
use Auth;
use View;

class NotificationHelper
{

    private $notiweb;
    private $notiemail;

    private $rcver;

    protected $email = true;
    protected $notificationWeb = true; // web & native notification
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

    public function setConfig($param)
    {
        if( is_array($param) ){
            foreach( $param as $key => $value ){
                switch($key){
                    case 'email':
                        $this->email = $value;
                        break;
                    case 'notification' :
                        $this->notificationWeb = $value;
                        break;
                    case 'sms' :
                        $this->sms = $value;
                        break;
                }
            }
        }
    }

    public function sendNotificationToUser( $user, $templateCode, $extraParam=[] )
    {
        $content = $this->getTemplate($templateCode);
        if( !$content )
            return $this->response;

        if( !is_array($user) )
            $user = [$user];

        foreach( $user as $rcver ){

            DB::beginTransaction();

            $this->rcver = $rcver;

            $userInfo = $this->getUserInformation($rcver);
            $data = array_merge($userInfo, $extraParam);

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
        if( $rcver instanceof DepotSalesrep )
            return $this->getDepotSalesrepInfo($rcver);

        if( $rcver instanceof ClientContact )
            return $this->getClientPicInfo($rcver);

    }

    private function getDepotSalesrepInfo($salesrep)
    {
        return [
            'name'      => $salesrep->name,
            'email'     => $salesrep->email,
            'mobile'    => $salesrep->hand_phone,
            'role'      => $salesrep->role,
            'user_id'   => $salesrep->emp_code,
            'salutation' => '',
            'token'     => $salesrep->tokens
        ];
    }

    private function getClientPicInfo($clientPic)
    {
        return [
            'name'      => $clientPic->name,
            'email'     => $clientPic->email,
            'mobile'    => $clientPic->phone,
            'role'      => '',
            'user_id'   => $clientPic->id,
            'salutation' => '',
            'token'     => $clientPic->tokens
        ];
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

            if( $this->rcver instanceof DepotSalesrep  ){
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
    }

    private function sendNotificationWeb($user, $views, $data)
    {
        if( $this->isNotificationWeb() ){
            $data['template'] = $views;

            $msg = $this->getMessageObject('webnoti', $data);

            $this->response['notification_web'] = $this->notiweb->sendNotificationToUser($user, $this->getMessageObject('webnoti', $data));

            if( $this->rcver instanceof DepotSalesrep  ){
                $content = [
                    'content' => $msg['msg'],
                    'subject' => $msg['msg'],
                    'target'  => $msg['url'] ?? null
                ];
                $this->addToDatabase($this->rcver, Notification::NOTIFICATION_TYPE_WEB_PUSH, $content);
            }
        }
    }

    private function addToDatabase($user, $type, $content)
    {
        //TODO:: Need to add in polymorphic colums to support client contact

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
