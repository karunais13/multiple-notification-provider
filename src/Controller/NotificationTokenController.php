<?php

namespace Karu\NpNotification\Controller;

use Karu\NpNotification\Models\NotificationToken;
use Illuminate\Http\Request;

use DB;
use App\Http\Controllers\Controller;

class NotificationTokenController extends Controller
{

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $userClassType, $userId)
    {
        try{

            $userClassList = config('notification.user_type');
            if( !array_key_exists($userClassType, $userClassList) )
                throw new \Exception('User Class Doesn\'t Exists');

            DB::beginTransaction();

            $modal = new NotificationToken();
            $noti = $modal->createUpdate($request, $userId, $userClassList[$userClassType]);
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
}
