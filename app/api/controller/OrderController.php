<?php

namespace app\api\controller;

use app\admin\model\Agent;
use app\admin\model\Shop;
use app\admin\model\ShopGoodsOrders;
use app\admin\model\ShopGoodsOrdersAfter;
use app\admin\model\ShopGoodsOrdersAssess;
use app\admin\model\UsersLayer;
use app\api\basic\Base;
use app\api\common\library\Pay;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use plugin\admin\app\common\Util;
use plugin\admin\app\model\Dict;
use plugin\admin\app\model\User;
use support\Request;

class OrderController extends Base
{
    function orderList(Request $request)
    {
        $status = $request->post('status'); #状态 0=全部,1=待付款,2=待发货,3=待收货,4=已完成,5=售后
        $rows = ShopGoodsOrders::with(['goods'])->where('user_id', $request->user_id)
            ->when(!empty($status), function (Builder $query) use ($status) {
                if ($status == 1) {
                    $query->where('status', 0);
                }
                if ($status == 2) {
                    $query->where('status', 1);
                }
                if ($status == 3) {
                    $query->where('status', 2);
                }
                if ($status == 4) {
                    $query->whereIn('status', [10, 11]);
                }
                if ($status == 5) {
                    $query->whereIn('status', [4, 5, 6, 7, 8, 9]);
                }
            })
            ->orderByDesc('id')
            ->paginate()
            ->getCollection()
            ->each(function (ShopGoodsOrders $item) {
                if ($item->status == 0) {
                    $expire_time = $item->created_at->addMinutes(15)->timestamp - Carbon::now()->timestamp;
                    $item->setAttribute('expire_time', $expire_time);
                }
                if ($item->goods->type == 0) {
                    $image = $item->sku->goods->image;
                    $sku_name = $item->sku->name;
                } else {
                    $image = $item->goods->image;
                    $sku_name = null;
                }
                $item->setAttribute('image', $image);
                $item->setAttribute('sku_name', $sku_name);
                $item->setAttribute('goods_name', $item->goods->name);
            });
        return $this->success('请求成功', $rows);
    }

    function orderDetail(Request $request)
    {
        $order_id = $request->post('order_id');
        $order = ShopGoodsOrders::getOrderById($order_id);
        $order->load(['address', 'goods', 'shop', 'after']);
        if ($order->status == 0) {
            $expire_time = $order->created_at->addMinutes(15)->timestamp - Carbon::now()->timestamp;
            $order->setAttribute('expire_time', $expire_time);
        }
        if ($order->goods->type == 0) {
            $image = $order->sku->goods->image;
            $sku_name = $order->sku->name;
        } else {
            $image = $order->goods->image;
            $sku_name = null;
        }
        $order->setAttribute('image', $image);
        $order->setAttribute('sku_name', $sku_name);
        $order->setAttribute('goods_name', $order->goods->name);
        return $this->success('请求成功', $order);
    }

    function orderPay(Request $request)
    {
        $ordersn = $request->post('ordersn');
        $pay_type = $request->post('pay_type');
        $user = User::getUserById($request->user_id);
        $order = ShopGoodsOrders::where(['ordersn' => $ordersn, 'user_id' => $request->user_id, 'status' => 0])->where('pay_amount', '<>', 0)->first();
        if (!$order) {
            return $this->fail('订单不存在');
        }

        $ret = Pay::pay($pay_type, $order->pay_amount, $order->ordersn, '购买商品', 'goods', $user->openid);
        return $this->success('成功', $ret);
    }

    function delete(Request $request)
    {
        $order_id = $request->post('order_id');
        $order = ShopGoodsOrders::getOrderById($order_id);
        if ($order->status != 3) {
            return $this->fail('订单状态错误');
        }
        $order->delete();
        return $this->success();
    }

    function cancel(Request $request)
    {
        $order_id = $request->post('order_id');
        $order = ShopGoodsOrders::getOrderById($order_id);
        if ($order->status != 0) {
            return $this->fail('订单状态错误');
        }
        $order->status = 3;
        $order->save();

        return $this->success();
    }


    #确认收货
    function accept(Request $request)
    {
        $order_id = $request->post('order_id');
        $order = ShopGoodsOrders::getOrderById($order_id);
        if ($order->status != 2) {
            return $this->fail('订单状态错误');
        }
        $order->status = 11;

        $get_green_score = round($order->goods_amount * 0.2, 2);
        $order->get_green_score = $get_green_score;
        $order->await_green_score = $get_green_score;
        $order->save();
        //消费者获得20%绿色积分
        User::score($get_green_score, $order->user_id, '购买商品获得绿色积分', 'green_score');
        //商家获得20%绿色积分
        User::score($get_green_score, $order->shop->user_id, '成交订单获得绿色积分', 'green_score');

        $platform_get_money = round($order->goods_amount * $order->shop->rate / 100, 2);
        $shop_get_money = $order->pay_amount - $platform_get_money;
        User::score($shop_get_money, $order->shop->user_id, '成交订单获得结算金额', 'money');

        User::score($order->deduction_coupon_score * 0.8, $order->shop->user_id, '成交订单获得抵扣消费券结算', 'money');

        $order->platform_get_money = $platform_get_money;//让利金额
        $order->shop_get_money = $shop_get_money;
        $order->shop_get_green_score = $get_green_score;
        $order->save();


        //向上给消费商分红消费券
        $get_coupon_score = round($platform_get_money * 0.02, 2);
        $layer = UsersLayer::where(['user_id' => $order->user_id])
            ->whereIn('layer', [1, 3, 5, 7, 9, 11, 13, 15, 17, 19])
            ->whereHas('user', function (Builder $query) {
                $query->whereHas('consumer');
            })
            ->get();
        $layer->each(function (UsersLayer $item) use ($get_coupon_score) {
            if ($item->parent->green_score >= $get_coupon_score) {
                //如果是消费商 并且消费商的绿色积分足够开始释放
                User::score(-$get_coupon_score, $item->parent_id, '订单确认释放绿色积分', 'green_score');
                User::score($get_coupon_score, $item->parent_id, '订单确认产生消费券', 'coupon_score');
            }
        });
        //消费商收益
        if (!$order->user->consumer&&$order->user->parent->consumer){
            //自己不是消费商   上级是消费商
            if ($order->user->parent->consumer->type ==1) {
                //一级消费商拿到5%的让利收益
                $consumer_amount = round($platform_get_money * 0.05, 2);
            }else{
                //一级消费商拿到10%的让利收益
                $consumer_amount = round($platform_get_money * 0.1, 2);
            }
            User::score($consumer_amount, $order->user->parent_id, '订单确认产生消费商金额', 'consumer_amount');
        }
        return $this->success();
    }

    #评价
    function assess(Request $request)
    {
        $order_id = $request->post('order_id');
        $content = $request->post('content');
        $describe_rating = $request->post('describe_rating');
        $serve_rating = $request->post('serve_rating');
        $express_rating = $request->post('express_rating');
        $images = $request->post('images');
        $anonymity = $request->post('anonymity');
        $order = ShopGoodsOrders::getOrderById($order_id);
        if ($order->status != 11) {
            return $this->fail('订单状态错误');
        }
        $order->status = 10;
        $order->save();
        $rating = $describe_rating + $serve_rating + $express_rating;
        $rating = $rating == 0 ? $rating : $rating / 3;
        ShopGoodsOrdersAssess::create([
            'user_id' => $order->user_id,
            'order_id' => $order_id,
            'shop_id' => $order->shop_id,
            'goods_id' => $order->goods_id,
            'describe_rating' => $describe_rating,
            'serve_rating' => $serve_rating,
            'express_rating' => $express_rating,
            'sku_id' => $order->sku_id,
            'rating' => $rating,
            'images' => $images,
            'content' => $content,
            'anonymity' => $anonymity
        ]);
        $order->shop->increment('sale_num', $order->num);
        $order->shop->increment('assess_num');
        $order->goods->increment('sale_num', $order->num);
        $order->goods->increment('assess_num');
        $assess = ShopGoodsOrdersAssess::where('shop_id', $order->shop_id)->get();
        $new_describe_rating = $assess->avg('describe_rating');
        $new_serve_rating = $assess->avg('serve_rating');
        $new_express_rating = $assess->avg('express_rating');
        $new_rating = $new_describe_rating + $new_serve_rating + $new_express_rating;
        $new_rating = $new_rating == 0 ? $new_rating : $new_rating / 3;
        $order->shop->update([
            'describe_rating' => $new_describe_rating,
            'serve_rating' => $new_serve_rating,
            'express_rating' => $new_express_rating,
            'rating' => $new_rating
        ]);
        $assess = ShopGoodsOrdersAssess::where('goods_id', $order->goods_id)->get();
        $new_describe_rating = $assess->avg('describe_rating');
        $new_serve_rating = $assess->avg('serve_rating');
        $new_express_rating = $assess->avg('express_rating');
        $new_rating = $new_describe_rating + $new_serve_rating + $new_express_rating;
        $new_rating = $new_rating == 0 ? $new_rating : $new_rating / 3;
        $order->goods->update([
            'describe_rating' => $new_describe_rating,
            'serve_rating' => $new_serve_rating,
            'express_rating' => $new_express_rating,
            'rating' => $new_rating
        ]);
        return $this->success();
    }

    #申请售后
    function applySaleAfter(Request $request)
    {
        $order_id = $request->post('order_id');
        $type = $request->post('type');
        $item_type = $request->post('item_type');
        $reason = $request->post('reason');
        $content = $request->post('content');
        $images = $request->post('images');
        $mobile = $request->post('mobile');

        $order = ShopGoodsOrders::getOrderById($order_id);
        if (!in_array($order->status, [1, 2])) {
            return $this->fail('订单状态错误');
        }
        $order->after_before_status = $order->status;
        $order->status = 4;
        $order->save();

        ShopGoodsOrdersAfter::create([
            'user_id' => $order->user_id,
            'order_id' => $order_id,
            'type' => $type,
            'item_type' => $item_type,
            'reason' => $reason,
            'content' => $content,
            'images' => $images,
            'mobile' => $mobile
        ]);
        return $this->success();
    }

    #撤销售后
    function cancelAfter(Request $request)
    {
        $order_id = $request->post('order_id');
        $order = ShopGoodsOrders::getOrderById($order_id);
        if (!in_array($order->status, [4, 5])) {
            return $this->fail('订单状态错误');
        }
        $order->status = $order->after_before_status;
        $order->after->status = 3;
        $order->after->save();
        $order->save();
        return $this->success();
    }

    function getExpressList(Request $request)
    {
        $rows = Dict::get('express');
        return $this->success('请求成功', $rows);
    }

    #退货
    function saleReturn(Request $request)
    {
        $order_id = $request->post('order_id');
        $express_id = $request->post('express_id');
        $waybill = $request->post('waybill');
        $express_name = Util::getDictNameByValue('express', $express_id);
        $order = ShopGoodsOrders::getOrderById($order_id);
        if ($order->status != 5) {
            return $this->fail('订单状态错误');
        }
        $order->status = 6;
        $order->save();
        $order->after->update([
            'express_name' => $express_name,
            'waybill' => $waybill,
        ]);
        return $this->success();
    }

    function getReturnAddress(Request $request)
    {
        $order_id = $request->post('order_id');
        $order = ShopGoodsOrders::getOrderById($order_id);
        $shop = $order->shop;
        return $this->success('请求成功', [
            'return_name' => $shop->return_name,
            'return_mobile' => $shop->return_mobile,
            'return_address' => $shop->return_address,
            'return_province' => $shop->return_province,
            'return_city' => $shop->return_city,
            'return_region' => $shop->return_region,
        ]);
    }


}
