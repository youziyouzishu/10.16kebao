<?php

namespace app\queue\redis;

use app\admin\model\ShopGoodsOrders;
use app\admin\model\UsersDrawLog;
use plugin\admin\app\model\User;
use Webman\RedisQueue\Consumer;

class Order implements Consumer
{
    // 要消费的队列名
    public $queue = 'order';

    // 连接名，对应 plugin/webman/redis-queue/redis.php 里的连接`
    public $connection = 'default';

    // 消费
    public function consume($data)
    {
        switch ($data['event']){
            case 'order_expire':
                // 订单过期
                $order = ShopGoodsOrders::getOrderById($data['order_id']);
                if ($order->status == 0){
                    //如果还没支付
                    $order->status = 3;
                    $order->save();
                    //返还积分和优惠额度
                    User::score($order->deduction_green_score,$order->user_id,'订单未支付返还抵扣绿色积分','green_score',false);
                    User::score($order->deduction_confirm_score,$order->user_id,'订单未支付返还抵扣确权积分','confirm_score',false);
                    User::score($order->deduction_develop_score,$order->user_id,'订单未支付返还抵扣拓展积分','develop_score',false);
                    User::score($order->deduction_coupon_score,$order->user_id,'订单未支付返还抵扣优惠券余额','coupon_score',false);

                    $row = UsersDrawLog::where('ordersn',$order->ordersn)->first();
                    if ($row){
                        $row->status = 1;
                        $row->save();
                    }
                }
                break;
            case 'order_accept':
                $order = ShopGoodsOrders::getOrderById($data['order_id']);
                if ($order->status == 2){
                    //如果还没支付
                    $order->status = 11;
                    $get_green_score = round($order->goods_amount * 0.2, 2);
                    if ($get_green_score != 0) {
                        //返还绿色积分
                        $order->get_green_score = $get_green_score;
                        $order->save();
                        User::score($get_green_score, $order->user_id, '购买商品获得绿色积分', 'green_score');
                    }

                    $platform_get_money = round($order->pay_amount * $order->shop->rate / 100);
                    $shop_get_money = $order->pay_amount - $platform_get_money;
                    User::score($shop_get_money, $order->shop->user_id, '成交订单获得金额', 'money');
                    $order->save();
                }
                break;
            case 'cancel':
                // 订单取消
                break;
        }
    }
            
}
