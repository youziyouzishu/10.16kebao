<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;
use plugin\admin\app\model\User;

/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property int $order_id 订单
 * @property int $type 类型:0=退货退款,1=我要退款(无需退货)
 * @property int $item_type 货物状态:0=未收到货,1=已收到货
 * @property string $reason 申请原因
 * @property string $content 申请说明
 * @property string $images 图片
 * @property string $mobile 联系方式
 * @property int $status 状态:0=待审核,1=待退货
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopGoodsOrdersAfter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopGoodsOrdersAfter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopGoodsOrdersAfter query()
 * @property string $express_name 退货快递公司
 * @property string $waybill 退货运单号
 * @property-read \app\admin\model\ShopGoodsOrders|null $orders
 * @property-read User|null $user
 * @property string $reject_reason 拒绝原因
 * @property-read mixed $status_text
 * @mixin \Eloquent
 */
class ShopGoodsOrdersAfter extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_shop_goods_orders_after';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id', 'order_id', 'type', 'item_type', 'reason', 'content', 'images', 'mobile','status'
    ];

    function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    function orders()
    {
        return $this->belongsTo(ShopGoodsOrders::class, 'order_id', 'id');
    }


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
            0 => '待售后',
            1 => '通过',
            2 => '驳回',
            3 => '撤销售后',
        ];
    }




}
