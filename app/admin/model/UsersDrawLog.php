<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;
use plugin\admin\app\model\User;
use support\exception\BusinessException;


/**
 * 
 *
 * @property int $id 主键
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property int $user_id 用户
 * @property int $type 消耗类型:1=绿色积分,2=确权积分
 * @property string $score 消耗积分
 * @property int $goods_id 中奖商品
 * @property int $status 状态:0=未中奖,1=待领取,2=已领取
 * @property string $mark 备注
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersDrawLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersDrawLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersDrawLog query()
 * @property string $ordersn 订单编号
 * @property-read User|null $user
 * @mixin \Eloquent
 */
class UsersDrawLog extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_users_draw_log';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'type',
        'score',
        'goods_id',
        'status',
        'mark',
        'ordersn'
    ];

    public static function getDrawById($id)
    {
        $row = self::find($id);
        if (!$row){
            throw new BusinessException('奖品不存在',1);
        }
        return $row;
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }
    
    
    
}
