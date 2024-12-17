<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;
use plugin\admin\app\model\User;

/**
 * 
 *
 * @property int $id 主键
 * @property int $order_id 订单
 * @property int $user_id 用户
 * @property int $shop_id 商铺
 * @property int $goods_id 商品
 * @property int $sku_id 规格
 * @property float $describe_rating 描述评分
 * @property float $serve_rating 服务评分
 * @property float $express_rating 物流评分
 * @property string $images 图片
 * @property string $content 补充说明
 * @property int $anonymity 匿名:0=否,1=是
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopGoodsOrdersAssess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopGoodsOrdersAssess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopGoodsOrdersAssess query()
 * @property float $rating 综合评价
 * @property-read \app\admin\model\ShopGoods|null $goods
 * @property-read \app\admin\model\Shop|null $shop
 * @property-read \app\admin\model\ShopGoodsSku|null $sku
 * @property-read User|null $user
 * @mixin \Eloquent
 */
class ShopGoodsOrdersAssess extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_shop_goods_orders_assess';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'order_id', 'user_id', 'shop_id', 'goods_id', 'describe_rating', 'serve_rating', 'express_rating', 'images', 'content', 'anonymity','rating','sku_id'
    ];

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

    function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }



}
