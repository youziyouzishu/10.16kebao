<?php

namespace app\api\controller;

use app\admin\model\ShopGoods;
use app\admin\model\ShopGoodsOrders;
use app\api\basic\Base;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use plugin\admin\app\model\Dict;
use support\Cache;
use support\Request;
use Webman\RedisQueue\Client;
use Webman\Util;

class IndexController extends Base
{
    protected $noNeedLogin = ['*'];
    public function index(Request $request)
    {
        $order = ShopGoodsOrders::where(['ordersn' => '20241216675FCC8C8C4FD', 'status' => 0])->first();
        if (!$order) {
            return $this->fail('订单不存在');
        }
        $order->status = 1;
        $order->pay_time = date('Y-m-d H:i:s');
        $order->goods->increment('sale_num'); //增加商品销量
        $order->goods->shop->increment('sale_num'); //增加门店销量
        $order->save();
        #自动确认收货
        // 队列名
        $queue = 'order';
        // 数据，可以直接传数组，无需序列化
        $data = ['order_id' => $order->id, 'event' => 'order_accept'];
        Client::send($queue, $data, 60*60*24*3);
        return $this->success();
    }

}
