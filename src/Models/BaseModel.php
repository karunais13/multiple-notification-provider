<?php

namespace Karu\NpNotification\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public function resCustom( $succeeded, $message = '', $objects = [] )
    {
        return [
            'succeeded'		=> $succeeded,
            'code'			=> 0,
            'message'		=> $message,
            'objects'		=> $objects
        ];
    }

    public function processException($exception, $statusCode = 200)
    {
        if( env('APP_DEBUG') || strtolower(env('APP_ENV')) != "production" )
            return $this->resCustom(FALSE, $exception->getMessage()." Line :".$exception->getLine()." ".$exception->getFile());
        else
            return $this->resCustom(FALSE, __('Server Error'));
    }
}
