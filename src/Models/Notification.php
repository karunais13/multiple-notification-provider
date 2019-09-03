<?php

namespace Karu\NpNotification\Models;

use Carbon\Carbon;

class Notification extends BaseModel
{
    const
        NOTIFICATION_STATUS_ACTIVE     = 1,
        NOTIFICATION_STATUS_INACTIVE   = 0,

        NOTIFICATION_TYPE_EMAIL          = 1,
        NOTIFICATION_TYPE_WEB_PUSH       = 2,
        NOTIFICATION_TYPE_NATIVE_PUSH    = 3,
        NOTIFICATION_TYPE_SMS            = 4
    ;

    protected $fillable = [
       'emp_code', 'type', 'content', 'is_read', 'status', 'subject', 'target'
    ];

    protected $dates  = [
        'created_at', 'updated_at'
    ];

    protected $hidden = [
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
        return $query->where('type', self::NOTIFICATION_TYPE_WEB_PUSH);
    }

    public function scopeIsPush( $query )
    {
        return $query->where('type', self::NOTIFICATION_TYPE_NATIVE_PUSH);
    }

    public function scopeIsSms( $query )
    {
        return $query->where('type', self::NOTIFICATION_TYPE_SMS);
    }

    public function scopeIsEmail( $query )
    {
        return $query->where('type', self::NOTIFICATION_TYPE_EMAIL);
    }


    public function updateRead($id)
    {
        try{
            $update  = $this->where('id', $id)->active()->update(['is_read' => true, 'updated_at' => Carbon::now()]);
            if( !$update )
                return response($this->resCustom(FALSE), 200);

            return response($this->resCustom(true, '', $update), 200);
        } catch( \Exception $e){
            return $this->processException($e);
        }
    }

}
