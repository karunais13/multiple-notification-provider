<?php


namespace Karu\NpNotification;

use Karu\NpNotification\Models\Notification;
use Karu\NpNotification\Traits\DataGetter;
use Karu\NpNotification\Traits\Validation;
use Karu\NpNotification\Helpers\OneSignalHelper;
use Karu\NpNotification\Helpers\EmailHelper;

use Carbon\Carbon;

class Sender
{
    public $content_by_country;
    public $notiweb;
    public $notiemail;
    public $notimobile;

    use Validation, DataGetter;

    public function __construct()
    {
        $this->content_by_country = config('notification.template_content_by_country');

        switch( config('notification.service.web') ){
            case 'onesignal':
                $this->notiweb		= new OneSignalHelper();
                break;
        }

        switch( config('notification.service.mobile') ){
            case 'onesignal':
                $this->notimobile   = new OneSignalHelper();
                break;
        }

        switch( config('notification.service.email') ){
            case 'default':
                $this->notiemail    = new EmailHelper();
                break;
        }
    }

    public function sendEmail($user, $views, $data)
    {
        if( $this->isEmail() && count(array_filter(array_values($views))) > 0){
            $data['template'] = $views;

            $msg = $this->getMessageObject('email', $data);

            $this->response['email'] = $this->notiemail->sendNotificationToUser($user, $msg);

            $html  = !empty($msg['content']['view']) ? $this->renderContent($msg['content']['view'], $msg['content']['data']) : "";
            $sub  = !empty($msg['content']['view']) ? $this->renderSubject($msg['subject']['view'], $msg['subject']['data']) : "";
            $content = [
                'content' => $html,
                'subject' => $sub,
                'target'  => $data['url'] ?? null
            ];
            $this->addToDatabase($this->rcver, $data['user_type'], NOTIFICATION_TYPE_EMAIL, $content);
        }
    }

    public function sendNotificationWeb($user, $views, $data)
    {
        if( $this->isNotificationWeb() && count(array_filter(array_values($views))) > 0 ){
            $data['template'] = $views;

            $msg = $this->getMessageObject('webnoti', $data);

            $this->response['notification_web'] = $this->notiweb->sendNotificationToUser($user, $this->getMessageObject('webnoti', $data));

            $content = [
                'content' => $msg['msg'],
                'subject' => $msg['msg'],
                'target'  => $msg['url'] ?? null
            ];
            $this->addToDatabase($this->rcver, $data['user_type'], NOTIFICATION_TYPE_WEB_PUSH, $content);
        }
    }

    public function sendNotificationMobile($user, $views, $data)
    {
        if( $this->isNotificationMobile() && count(array_filter(array_values($views))) > 0 ){
            $data['template'] = $views;

            $msg = $this->getMessageObject('mobilenoti', $data);

            $this->response['notification_mobile'] = $this->notimobile->sendNotificationToUser($user, $this->getMessageObject('mobilenoti', $data));

            $content = [
                'content' => $msg['msg'],
                'subject' => $msg['msg'],
                'target'  => $msg['url'] ?? null
            ];
            $this->addToDatabase($this->rcver, $data['user_type'], NOTIFICATION_TYPE_NATIVE_PUSH, $content);
        }
    }

    public function addToDatabase($user, $userType, $type, $content)
    {
        if( config('notification.log_notification') ){
            $data = [
                'notiuser_id'  => $user->emp_code,
                'notiuser_type' => $userType,
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
