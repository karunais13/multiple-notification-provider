<?php

namespace Karu\NpNotification\Helpers;

use Mail;

class EmailHelper
{
    public function sendNotificationToUser( $userInfo, $content)
    {
        try {
            $sendMail = Mail::send($content['content']['view'], $content['content']['data'], function ($message) use($content, $userInfo)
            {
                $subject = $content['subject']['view'] ? view($content['subject']['view'], $content['subject']['data'])->render() : '';
                $message->to($userInfo['email']);
                $message->subject($subject);
            });

            return [TRUE, 'Success'];

        } catch(\Exception $e){

            return [FALSE, $e->getMessage()];
        }
    }

    private function processParams( $params )
    {
        $params     = $params;
        $arr = [
            null,
            null,
            null,
            null,
            null,
            null
        ];
        if( array_key_exists('msg', $params) ){
            $arr[0] = $params['msg'];
        }

        if( array_key_exists('user_id', $params) ){
            $arr[1] = $params['user_id'];
        }

        if( array_key_exists('tags', $params) ){
            $arr[1] = $params['tags'];
        }

        if( array_key_exists('url', $params) )
            $arr[2] = $params['url'];

        if( array_key_exists('data', $params) )
            $arr[3] = $params['data'];

        if( array_key_exists('buttons', $params) )
            $arr[4] = $params['buttons'];

        if( array_key_exists('schedule', $params) )
            $arr[5] = $params['schedule'];

        $this->createNotificationOnDatabase($params);

        return $arr;
    }

    private function createNotificationOnDatabase( $params )
    {
        $message = $params['msg'];
        if( array_key_exists('to', $params) ){
            $to = $params['to'];

            $url = '';
            if( array_key_exists('url', $params) )
                $url = $params['url'];

            $from = $to;
            if( array_key_exists('from', $params) )
                $from = $params['from'];
        }
    }
}
