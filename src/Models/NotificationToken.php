<?php

namespace Karu\NpNotification\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Validator;

class NotificationToken extends BaseModel
{
    use SoftDeletes;

    const
        NOTIFICATION_TOKEN_ACTIVE      = 1,
        NOTIFICATION_TOKEN_INACTIVE    = 0
    ;


    protected $createdBy = false;
    protected $updatedBy = false;

    protected $table = 'notification_token';

    protected $fillable = [
        'type', 'token','is_login', 'notitokenable_id', 'notitokenable_type'
    ];

    protected $dates  = [
        'created_at', 'updated_at', 'last_updated_at'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function __construct()
    {
        parent::__construct();

        $this->table = config('notification.tables.notification_token');
    }

    public function notitokenable()
    {
        return $this->morphTo();
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::NOTIFICATION_TOKEN_ACTIVE);
    }

    public function scopeIsLogin($query)
    {
        return $query->where('is_login', TRUE);
    }

    public function isEligibleNotification()
    {
        if( isset($this->is_login) && !empty($this->is_login) &&
            isset($this->status) && $this->status == self::NOTIFICATION_TOKEN_ACTIVE )
            return true;

        return false;
    }

    public function createUpdate($request, $userId=null, $userClass=null)
    {
        if( !$userId || !$userClass )
            return $this->resCustom(false, "User ID & User Class is needed");

        $rules = [
            'type' => sprintf('required|in:%s,%s', NOTIFICATION_TOKEN_TYPE_WEB, NOTIFICATION_TOKEN_TYPE_IOS, NOTIFICATION_TOKEN_TYPE_ANDROID),
        ];
        $validator    = Validator::make($request->all(), $rules);
        if( $validator->fails() ){
            return $this->resCustom(false, 'Validation Failed');
        }

        $status = self::NOTIFICATION_TOKEN_ACTIVE;
        if( !$request->action || $request->action === 'false' )
            $status = self::NOTIFICATION_TOKEN_INACTIVE;

        $value  = [
            'notitokenable_id'   => $userId,
            'notitokenable_type'   => $userClass,
            'is_login'  => true,
            'status'    => $status,
        ];


        //TODO :: Update or Create function by laravel not working.
        $exist = $this->where('token', $request->token)->where('type', $request->type)->exists();
        if( $exist ){
            $update = $this->where('token', $request->token)->where('type', $request->type)->update($value);
            if( $update ){
                return $this->resCustom(TRUE);
            }
        }else {
            $anchor = [
                'token'   => $request->token,
                'type'    => $request->type
            ];
            $value = array_merge($value, $anchor);
            $insert = $this->insert($value);
            if ($insert) {
                return $this->resCustom(true);

            }
        }

        $instalation        = NotificationToken::updateOrCreate($anchor, $value);
        if( $instalation ){
            return $this->resCustom(TRUE);
        }

        return $this->resCustom(FALSE);
    }

    public function unsubscribeUser($userId, $userClassType, $token)
    {
        $userClassList = config('notification.user_type');

        return $this->where('notitokenable_id', $userId)
            ->where('notitokenable_type', $userClassList[$userClassType])
            ->isLogin()
            ->active()
            ->where('token', $token)
            ->update(['is_login' => 0 ]);
    }

}
