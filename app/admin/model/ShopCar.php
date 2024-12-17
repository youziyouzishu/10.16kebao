<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;
use plugin\admin\app\model\User;
use support\exception\BusinessException;


/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property int $sku_id 规格
 * @property int $num 数量
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopCar query()
 * @property int $shop_id 商家
 * @property int $goods_id 商品
 * @property-read \app\admin\model\ShopGoods|null $goods
 * @property-read \app\admin\model\Shop|null $shop
 * @property-read \app\admin\model\ShopGoodsSku|null $sku
 * @property-read User|null $user
 * @property string $tag 型号
 * @mixin \Eloquent
 */
class ShopCar extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_shop_car';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = ['user_id','sku_id','num','shop_id','goods_id','tag'];

    function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    function sku()
    {
        return $this->belongsTo(ShopGoodsSku::class,'sku_id','id');
    }

    function goods()
    {
        return $this->belongsTo(ShopGoods::class,'goods_id','id');
    }

    function shop()
    {
        return $this->belongsTo(Shop::class,'shop_id','id');
    }

    public static function getShopCarById($id)
    {
        $row = self::find($id);
        if (!$row){
            throw new BusinessException('购物车不存在',1);
        }
        return $row;
    }

    
    
    
}
