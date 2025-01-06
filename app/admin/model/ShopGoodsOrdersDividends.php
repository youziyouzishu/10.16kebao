<?php

namespace app\admin\model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use plugin\admin\app\model\Base;
use plugin\admin\app\model\User;
use support\exception\BusinessException;



/**
 * 
 *
 * @property int $id 主键
 * @property int $order_id 订单
 * @property int $user_id 用户
 * @property string $get_coupon_score 获得消费券
 * @property string $expend_green_score 消耗绿色积分
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static Builder<static>|ShopGoodsOrdersDividends newModelQuery()
 * @method static Builder<static>|ShopGoodsOrdersDividends newQuery()
 * @method static Builder<static>|ShopGoodsOrdersDividends onlyTrashed()
 * @method static Builder<static>|ShopGoodsOrdersDividends query()
 * @method static Builder<static>|ShopGoodsOrdersDividends withTrashed()
 * @method static Builder<static>|ShopGoodsOrdersDividends withoutTrashed()
 * @mixin \Eloquent
 */
class ShopGoodsOrdersDividends extends Base
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_shop_goods_orders_dividends';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'order_id',
        'user_id',
        'get_coupon_score',
        'expend_green_score',
    ];





}
