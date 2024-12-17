<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;

/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 所属用户
 * @property int $goods_id 所属商品
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersTrack newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersTrack newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersTrack query()
 * @property-read \app\admin\model\ShopGoods|null $goods
 * @mixin \Eloquent
 */
class UsersTrack extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_users_track';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id', 'goods_id'
    ];

    function goods()
    {
        return $this->belongsTo(ShopGoods::class, 'goods_id', 'id');
    }
    
    
    
}
