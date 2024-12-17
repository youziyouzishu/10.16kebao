<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;
use plugin\admin\app\model\User;


/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property int $to_user_id 转赠对象
 * @property string $charge 门店积分手续费
 * @property string $use_shop_score 消耗的门店积分
 * @property string $deposit_score 到账的门店积分
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopScoreLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopScoreLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopScoreLog query()
 * @property-read User|null $toUser
 * @property-read User|null $user
 * @mixin \Eloquent
 */
class ShopScoreLog extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_shop_score_log';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id', 'to_user_id', 'charge', 'use_shop_score', 'deposit_score'
    ];

    function toUser()
    {
        return $this->belongsTo(User::class);
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }



}
