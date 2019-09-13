<?php

namespace Karu\NpNotification\Helpers;

use OneSignal;

class OneSignalHelper
{
//    public function sendNotificationToAll( $params )
//    {
//        $params = $this->processParams($params);
//
//        return OneSignal::sendNotificationToAll($params[0], $params[1], $params[2], $params[3], $params[4], $params[5]);
//    }

//    public function sendNotificationToAllLogin( $params )
//    {
//        // need to add to logic to send to all members tht are active and login;
//        $params = $this->processParams($params);
//
//        return OneSignal::sendNotificationToAll($params[0], $params[1], $params[2], $params[3], $params[4], $params[5]);
//    }

//    public function sendNotificationUsingTags( $user, $params )
//    {
//        $params['to']   = $user;
//        $params = $this->processParams($params);
//
//        return OneSignal::sendNotificationUsingTags($params[0], $params[1], $params[2], $params[3], $params[4], $params[5]);
//    }

    public function sendNotificationToUser( $userInfo, $content )
    {
        $instalation    = $userInfo['token'];
        $params  = array_merge($userInfo, $content);
        if( $instalation->isNotEmpty() ){
            $params['to']   = $userInfo['user_id'];
            foreach( $instalation as $item ){
                if( $item->isEligibleNotification() ){
                    $params['user_id'] = $item->token;
                    $newParams = $this->processParams($params);
                    OneSignal::sendNotificationToUser($newParams[0], $newParams[1], $newParams[2], $newParams[3], $newParams[4], $newParams[5]);
                }
            }

            return [true, 'Success'];
        }

        return [false, 'Failed'];
    }

    private function processParams( $params )
    {
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
