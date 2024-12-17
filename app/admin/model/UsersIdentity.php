<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;
use plugin\admin\app\model\User;


/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property string $truename 真实姓名
 * @property string $mobile 手机号
 * @property string $cardnum 身份证号
 * @property string|null $expire_day 有效期
 * @property string $front 正面
 * @property string $back 反面
 * @property int $status 状态:0=待审核,1=通过,2=驳回
 * @property string $reason 驳回原因
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersIdentity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersIdentity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersIdentity query()
 * @property-read User|null $user
 * @mixin \Eloquent
 */
class UsersIdentity extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_users_identity';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = ['user_id','truename','mobile','cardnum','expire_day','front','back','status','reason'];

    function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

}
