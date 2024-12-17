<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;
use support\exception\BusinessException;

/**
 * 
 *
 * @property integer $id 主键(主键)
 * @property integer $goods_id 所属商品
 * @property string $name 规格名称
 * @property string $price 价格
 * @property integer $total 总数量
 * @property integer $num 剩余数量
 * @property string $image 规格图片
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopGoodsSku newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopGoodsSku newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopGoodsSku query()
 * @property-read \app\admin\model\ShopGoods|null $goods
 * @property array $tags 标签
 * @mixin \Eloquent
 */
class ShopGoodsSku extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_shop_goods_sku';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    function goods()
    {
        return $this->belongsTo(ShopGoods::class, 'goods_id');
    }

    public static function getSkuById($id){
        $row = self::find($id);
        if (!$row){
            throw new BusinessException('商品规格不存在',1);
        }
        return $row;
    }


}
