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
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property string $ordersn 订单编号
 * @property int $user_id 买家
 * @property int $shop_id 卖家
 * @property int $pay_type 支付方式:1=微信,2=支付宝
 * @property string $pay_amount 支付金额
 * @property string|null $pay_time 支付时间
 * @property int $sku_id 规格
 * @property int $goods_id 商品
 * @property int $num 数量
 * @property string $freight 运费
 * @property string $goods_amount 商品总额
 * @property string $deduction_green_score 抵扣绿色积分
 * @property string $deduction_confirm_score 抵扣确权积分
 * @property string $deduction_develop_score 抵扣拓展积分
 * @property string $get_green_score 获得绿色积分
 * @property int $status 状态:0=未支付,1=待发货,2=待收货,3=取消,4=申请售后,5=待退货,6=退货中,7=售后完成,8=售后取消,9=售后拒绝,10=已完成,11=待评价
 * @property int $address_id 收货地址
 * @method static Builder<static>|ShopGoodsOrders newModelQuery()
 * @method static Builder<static>|ShopGoodsOrders newQuery()
 * @method static Builder<static>|ShopGoodsOrders query()
 * @property string $deduction_coupon_score 抵扣优惠券额度
 * @property int $sector_id 所属板块
 * @property-read User|null $user
 * @property-read \app\admin\model\ShopGoods|null $goods
 * @property-read \app\admin\model\Shop|null $shop
 * @property-read \app\admin\model\ShopGoodsOrdersAfter|null $after
 * @property-read \app\admin\model\Address|null $address
 * @property-read mixed $status_text
 * @property-read \app\admin\model\ShopGoodsSku|null $sku
 * @property string|null $deliver_time 发货时间
 * @property string $deliver_express_name 发货快递公司
 * @property string $deliver_waybill 发货运单号
 * @property string $mark 订单备注
 * @property string $coupon_amount 优惠金额
 * @property string $get_shop_score 获得门店积分
 * @property string $tag 型号
 * @property string $combine_ordersn 合单订单号
 * @property string $shop_get_money 商家获得金额
 * @property string $platform_get_money 平台获得金额
 * @property string|null $deleted_at 删除时间
 * @property int $after_before_status 售后前状态
 * @method static Builder<static>|ShopGoodsOrders onlyTrashed()
 * @method static Builder<static>|ShopGoodsOrders withTrashed()
 * @method static Builder<static>|ShopGoodsOrders withoutTrashed()
 * @property int $admin_id 所属后台
 * @property string $shop_get_green_score 商家获得绿色积分
 * @property string $await_green_score 待释放绿色积分
 * @property string|null $last_release_at 上一次释放时间
 * @property string $should_pay_amount 应付金额
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \app\admin\model\ShopGoodsOrdersDividends> $dividends
 * @mixin \Eloquent
 */
class ShopGoodsOrders extends Base
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_shop_goods_orders';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'admin_id',
        'ordersn',
        'user_id',
        'shop_id',
        'pay_type',
        'pay_amount',
        'pay_time',
        'sku_id',
        'goods_id',
        'num',
        'freight',
        'goods_amount',
        'deduction_green_score',
        'deduction_confirm_score',
        'deduction_develop_score',
        'get_green_score',
        'type',
        'status',
        'address_id',
        'deduction_coupon_score',
        'sector_id',
        'goods_type',
        'mark',
        'tag',
        'combine_ordersn',
        'shop_get_money',
        'platform_get_money',
        'after_before_status',
        'shop_get_green_score',
        'deliver_time',
        'deliver_express_name',
        'deliver_waybill',
        'coupon_amount',
        'get_shop_score',
        'should_pay_amount',
        'await_green_score',
        'last_release_at',
    ];

    protected $appends = ['status_text'];


    function getStatusTextAttribute($value)
    {
        $value = $value ?: ($this->status ?? '');
        $list = $this->getStatusList();
        return $list[$value] ?? '';
    }

    public function getStatusList()
    {
        return [
            0 => '未支付',
            1 => '待发货',
            2 => '待收货',
            3 => '取消',
            4 => '申请售后',
            5 => '待退货',
            6 => '退货中',
            7 => '售后完成',
            8 => '售后取消',
            9 => '售后拒绝',
            10 => '已完成',
            11 => '待评价'
        ];
    }


    function user()
    {
        return $this->belongsTo(User::class);
    }

    function goods()
    {
        return $this->belongsTo(ShopGoods::class,'goods_id','id');
    }

    public static function getOrderById($id)
    {
        $row = self::find($id);
        if (!$row){
            throw new BusinessException('订单不存在',1);
        }
        return $row;
    }

    function shop()
    {
        return $this->belongsTo(Shop::class,'shop_id','id');
    }

    function after()
    {
        return $this->hasOne(ShopGoodsOrdersAfter::class,'order_id','id')->orderByDesc('id');
    }

    function address()
    {
        return $this->belongsTo(Address::class,'address_id','id');
    }

    function sku()
    {
        return $this->belongsTo(ShopGoodsSku::class,'sku_id','id');
    }

    function dividends()
    {
        return $this->hasMany(ShopGoodsOrdersDividends::class,'order_id','id');
    }



}
