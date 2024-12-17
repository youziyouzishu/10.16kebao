<?php

namespace app\api\controller;

use app\admin\model\PieceLog;
use app\admin\model\ShopGoods;
use app\admin\model\ShopGoodsOrders;
use app\api\basic\Base;
use plugin\admin\app\common\Util;
use plugin\admin\app\model\User;
use support\Request;

class PieceController extends Base
{
    function list(Request $request)
    {
        $coupon_zone = $request->post('coupon_zone');#券区:0=全部,1=一券区,2=十券区,3=百券区,4=千券区
        $rows = ShopGoods::where(['status'=>1,'type'=>1])->where('num','>',0)->when(!empty($coupon_zone),function ($query)use($coupon_zone){
            $query->where('coupon_zone',$coupon_zone);
        })->get();
        return $this->success('成功', $rows);
    }

    function detail(Request $request)
    {
        $goods_id = $request->post('goods_id');
        $row = ShopGoods::find($goods_id);
        return $this->success('成功', $row);
    }

    function pay(Request $request)
    {
        $address_id =  $request->post('address_id');
        $goods_id = $request->post('goods_id');
        $num = $request->post('num');
        $goods = ShopGoods::getGoodsById($goods_id);
        if ($goods->num < $num){
            return $this->fail('商品已售罄');
        }
        if ($goods->coupon_zone == 1){
            $pay_amount = 1;
        }elseif ($goods->coupon_zone == 2){
            $pay_amount = 10;
        }elseif ($goods->coupon_zone == 3){
            $pay_amount = 100;
        }elseif ($goods->coupon_zone == 4){
            $pay_amount = 1000;
        }else{
            return $this->fail('参数错误');
        }
        $pay_amount = $pay_amount * $num;
        $user = User::getUserById($request->user_id);
        if ($user->money < $pay_amount){
            return $this->fail('抵扣券不足');
        }
        User::score(-$pay_amount,$user->id,'拼夺商品','offset');
        User::score($pay_amount * 0.2,$user->id,'拼夺商品','green_score');

        PieceLog::create([
            'user_id' => $request->user_id,
            'goods_id' => $goods_id,
            'address_id' => $address_id,
            'num'=>$num
        ]);
        $goods->decrement('num',$num);
        if ($goods->num <= 0){
            // 生成总份数
            $totalTickets = 0;
            $ticketMap = [];

            //人数凑够 开始进行发奖
            $goods->status = 0;
            $goods->save();
            PieceLog::where('goods_id',$goods_id)->get()->each(function ($item)use(&$totalTickets,&$ticketMap){
                $item->status = 1;
                $item->save();
                for ($i = 0; $i < $item->num; $i++) {
                    $totalTickets++;
                    $ticketMap[$totalTickets] = $item->id; // 存储 PieceLog 的 ID
                }
            });
            // 随机抽取一个数字
            $randomTicket = rand(1, $totalTickets);
            $winner = PieceLog::find($ticketMap[$randomTicket]);
            $winner->winer = 1;
            $winner->save();
            if ($winner->goods->piece_type == 1){
                //直接付款
                $ordersn = Util::generateOrdersn();
                $ordersData = [
                    'ordersn' => $ordersn,
                    'user_id' => $winner->user_id,
                    'shop_id' => $winner->goods->shop_id,
                    'goods_id' => $winner->goods_id,
                    'goods_amount' => $winner->goods->original_price,
                    'address_id' => $address_id,
                    'status'=>1
                ];
                ShopGoodsOrders::create($ordersData);
            }elseif ($winner->goods->piece_type == 2){
                //得到消费券
                User::score($winner->goods->get_coupon_score,$winner->user_id,'拼夺商品','coupon_score');
            }
        }
        return  $this->success('拼夺成功');
    }

    function getPieceLog(Request $request)
    {
        $rows = PieceLog::with(['goods'])->where('user_id',$request->user_id)->paginate()->items();
        return $this->success('成功', $rows);
    }

}
