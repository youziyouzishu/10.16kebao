<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;


/**
 * 
 *
 * @property int $id 主键
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property int $user_id 用户
 * @property string $green_socre 消耗绿色积分
 * @property string $contribution_score 获得贡献值
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersSignLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersSignLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersSignLog query()
 * @property int $type 类型:1=视频签到,2=阅读签到
 * @mixin \Eloquent
 */
class UsersSignLog extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_users_sign_log';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id', 'green_socre', 'contribution_score','type'
    ];

    
    
    
}
