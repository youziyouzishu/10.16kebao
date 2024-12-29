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
        dump($data);
        switch ($data['event']){
            case 'order_expire':
                dump(1111);
                // 订单过期
                $order = ShopGoodsOrders::getOrderById($data['order_id']);
                if ($order->status == 0){
                    dump(2222);
                    //如果还没支付
                    $order->status = 3;
                    $order->save();
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
