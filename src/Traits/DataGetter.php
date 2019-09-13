<?php

namespace Karu\NpNotification\Traits;

trait DataGetter
{
    public function getUserInformation($rcver)
    {
        if( is_object($rcver) && method_exists($rcver, config('notification.user_info_method')) ){
            return $rcver->{config('notification.user_info_method')}();
        }

        return null;
    }

    public function getMessageObject($type, $data)
    {
        if( $this->content_by_country ){
            return $this->getContentByCountry($type, $data);
        }

        return $this->getContent($type, $data);
    }

    public function getContentByCountry($type, $data)
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
            case 'mobilenoti' :
                $content = $data['template']['content'] ? $this->renderContent(sprintf($data['template']['content'], strtolower($data['country_code'])), $data) : '';
                return [
                    'msg' => $content,
                    'url' => $data['url'] ?? null,
                    'data' => $data['data'] ?? null
                ];
                break;
            default :
                return [];
        }
    }

    public function getContent($type, $data)
    {
        switch($type){
            case 'email':
                return [
                    'subject' => [
                        'view' => $data['template']['subject'],
                        'data' => $data
                    ],
                    'content' => [
                        'view' => $data['template']['content'],
                        'data' => $data
                    ],
                ];
                break;
            case 'webnoti' :
            case 'mobilenoti' :
                $content = $data['template']['content'] ? $this->renderContent($data['template']['content'], $data) : '';
                return [
                    'msg' => $content,
                    'url' => $data['url'] ?? null,
                    'data' => $data['data'] ?? null
                ];
                break;
            default :
                return [];
        }
    }

    public function getTemplate($templateCode)
    {
        return config("notification.template.{$templateCode}");
    }

    public function renderContent($view, $data)
    {
        return view($view, $data)->render();
    }

    public function renderSubject($view, $data)
    {
        return view($view, $data)->render();
    }
}
