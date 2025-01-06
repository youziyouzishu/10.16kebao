<?php

namespace app\admin\model;


use plugin\admin\app\model\Base;

/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property int $goods_id 商品
 * @property int $status 拼夺状态:0=拼夺中,1=完成
 * @property int $winer 是否赢家:0=否,1=是
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PieceLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PieceLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PieceLog query()
 * @property int $address_id 收货地址
 * @property-read \app\admin\model\ShopGoods|null $goods
 * @property int $num 数量
 * @property string $pay_amount 支付抵扣券
 * @mixin \Eloquent
 */
class PieceLog extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_piece_log';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id', 'goods_id', 'status', 'winer', 'address_id','num','pay_amount'
    ];

    function goods()
    {
        return $this->belongsTo(ShopGoods::class, 'goods_id', 'id');
    }
    
    
    
}
