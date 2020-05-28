<?php

namespace Karu\NpNotification\Controller;

use Karu\NpNotification\Models\Notification;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use DB;

class NotificationController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        try{
            DB::beginTransaction();

            $modal = new Notification();
            $noti = $modal->updateRead($id);
            if( !$noti['succeeded'] ){
                DB::rollBack();
                return response($noti, 200);
            }

            DB::commit();

            return response($noti, 200);

        } catch(\Exception $e){
            return $this->processException($e);
        }
    }

    protected function processException($exception, $statusCode = 200)
    {
        if( env('APP_DEBUG') || strtolower(env('APP_ENV')) != "production" )
            return response(NpRes::resCustom(FALSE, $exception->getMessage()." Line :".$exception->getLine()." ".$exception->getFile()), $statusCode);
        else
            return response(NpRes::resCustom(FALSE, __('Server Error')), $statusCode);
    }
}
