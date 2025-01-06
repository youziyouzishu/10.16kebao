<?php

namespace app\admin\model;

use Illuminate\Database\Eloquent\Builder;
use plugin\admin\app\model\Base;
use plugin\admin\app\model\User;
use support\exception\BusinessException;

/**
 * 
 *
 * @property integer $id 主键(主键)
 * @property integer $user_id 所属用户
 * @property integer $shop_id 所属商家
 * @property integer $class_id 所属分类
 * @property integer $admin_id 所属后台
 * @property string $image 封面
 * @property string $video 视频
 * @property string $images 图片
 * @property string $name 商品名称
 * @property string $detail 商品详情
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property float $rating 商品评分
 * @property float $describe_rating 描述评分
 * @property float $serve_rating 服务评分
 * @property float $express_rating 物流评分
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopGoods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopGoods query()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \app\admin\model\ShopGoodsSku> $sku
 * @property-read mixed $sku_min_price
 * @property-read \app\admin\model\Shop|null $shop
 * @property-read User|null $user
 * @property int $assess_num 评价数量
 * @property string $freight 运费
 * @property int $type 类型:0=普通商品,1=拓客商品
 * @property int $sale_num 销量
 * @property int $collect_num 收藏数量
 * @property int $status 状态:0=下架,1=上架
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \app\admin\model\ShopGoodsOrdersAssess> $assess
 * @property string $original_price 原价
 * @property int $coupon_zone 券区:0=无,1=一券区,2=十券区,3=百券区,4=千券区
 * @property int $piece_type 拼夺类型0=无,1=实体商品,2=消费券
 * @property int $total 拼夺总数量
 * @property int $num 拼夺剩余数量
 * @property string $get_coupon_score 得到消费券额度
 * @property int $weigh 权重
 * @property-read \app\admin\model\GoodsClass|null $class
 * @mixin \Eloquent
 */
class ShopGoods extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_shop_goods';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'shop_id',
        'class_id',
        'admin_id',
        'image',
        'video',
        'images',
        'name',
        'detail',
        'freight',
        'type',
        'status',
        'assess_num',
        'sale_num',
        'collect_num',
        'rating',
        'describe_rating',
        'serve_rating',
        'express_rating',
        'original_price'
    ];

    protected $appends = ['sku_min_price'];


    public function getSkuMinPriceAttribute()
    {
        return $this->sku()->min('price');
    }


    function sku()
    {
        return $this->hasMany(ShopGoodsSku::class, 'goods_id', 'id')->where('num', '>', 0);
    }

    function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }


    function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    function class()
    {
        return $this->belongsTo(GoodsClass::class, 'class_id');
    }

    public static function getGoodsById($id)
    {
        $row = self::find($id);
        if (!$row){
            throw new BusinessException('商品不存在',1);
        }
        return $row;
    }

    public static function isCollect($user_id,$goods_id)
    {
        return UsersCollect::where('user_id', $user_id)->where('pro_id', $goods_id)->where('type', 'goods')->exists();
    }

    function assess()
    {
        return $this->hasMany(ShopGoodsOrdersAssess::class, 'goods_id', 'id');
    }


    
    
}
