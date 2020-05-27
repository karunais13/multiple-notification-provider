<?php

namespace Karu\NpNotification\Models;

use Carbon\Carbon;

class Notification extends BaseModel
{
    const
        NOTIFICATION_STATUS_ACTIVE     = 1,
        NOTIFICATION_STATUS_INACTIVE   = 0
    ;

    protected $fillable = [
       'emp_code', 'type', 'content', 'is_read', 'status', 'subject', 'target'
    ];

    protected $dates  = [
        'created_at', 'updated_at'
    ];

    public function __construct()
    {
        parent::__construct();

        $this->table = config('notification.tables.notification_store');
    }


    public function scopeActive( $query )
    {
        return $query->where('status', self::NOTIFICATION_STATUS_ACTIVE);
    }

    public function scopeNotRead( $query )
    {
        return $query->where('is_read', FALSE);
    }

    public function scopeIsWebPush( $query )
    {
        return $query->where('type', NOTIFICATION_TYPE_WEB_PUSH);
    }

    public function scopeIsPush( $query )
    {
        return $query->where('type', NOTIFICATION_TYPE_NATIVE_PUSH);
    }

    public function scopeIsSms( $query )
    {
        return $query->where('type', NOTIFICATION_TYPE_SMS);
    }

    public function scopeIsEmail( $query )
    {
        return $query->where('type', NOTIFICATION_TYPE_EMAIL);
    }

    public function notiuser()
    {
        return $this->morphTo();
    }

    public function updateRead($id)
    {
        try{
            $update  = $this->where('id', $id)->active()->update(['is_read' => true, 'updated_at' => Carbon::now()]);
            if( !$update )
                return $this->resCustom(FALSE);

            return $this->resCustom(true, '', $update);
        } catch( \Exception $e){
            return $this->processException($e);
        }
    }

    public function getUnReadUserNotificationList( $userId, $userType, $notiType = NOTIFICATION_TYPE_WEB_PUSH, $passDay = 1, $limit=20, $offset=0)
    {
        $notificationList = $this->where('notiuser_id', $userId)
            ->where('notiuser_type', $userType)
            ->when($passDay > 0, function($q)use($passDay){
                $q->where(function($w) use($passDay){
                    $w->where('created_at', '>=', Carbon::now()->subDays($passDay))
                        ->orWhere('is_read', FALSE);
                });
            })
            ->active()
            ->orderBy('id', 'DESC');

        switch ($notiType){
            case NOTIFICATION_TYPE_WEB_PUSH :
                $notificationList = $notificationList->isWebPush();
                break;
            case NOTIFICATION_TYPE_NATIVE_PUSH :
                $notificationList = $notificationList->isPush();
                break;
            case NOTIFICATION_TYPE_EMAIL :
                $notificationList = $notificationList->isEmail();
                break;
            case NOTIFICATION_TYPE_SMS :
                $notificationList = $notificationList->isSms();
                break;
        }

        return $notificationList->limit($limit)->offset($offset)->get();
    }

}
