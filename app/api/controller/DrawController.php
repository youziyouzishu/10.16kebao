<?php

namespace app\api\controller;

use app\admin\model\ShopGoods;
use app\admin\model\UsersDrawLog;
use app\api\basic\Base;
use plugin\admin\app\model\Option;
use plugin\admin\app\model\User;
use support\Request;

class DrawController extends Base
{

    function getConfig()
    {
        $opengreen = getConfig('draw', '是否使用绿色积分');
        $openconfirm = getConfig('draw', '是否使用确权积分');
        $prizes = ShopGoods::where(['sector_id' => 6, 'status' => 1])->get();
        $name = 'admin_config';
        $config = Option::where('name', $name)->value('value');
        $config = json_decode($config);
        $prize = $config->prize;
        // 补充“谢谢惠顾”
        while ($prizes->count() < 8) {
            $prizes->push((object)['id' => 0, 'name' => '感谢参与']);
        }
        $green = false;
        $confirm = false;
        if ($opengreen == '是') {
            $green = true;
        }
        if ($openconfirm == '是') {
            $confirm = true;
        }
        return $this->success('成功', [
            'green' => $green,
            'green_score' => getConfig('draw', '消耗绿色积分'),
            'confirm' => $confirm,
            'confirm_score' => getConfig('draw', '消耗确权积分'),
            'prizes' => $prizes,
            'explain'=>$prize->prize_explain
        ]);
    }


    function doDraw(Request $request)
    {
        $type = $request->post('type');#1绿色积分 2确权积分

        $prizes = ShopGoods::where(['sector_id' => 6, 'status' => 1])->get();
        if ($prizes->count() > 8) {
            return $this->fail('当前人数较多,请稍后再试');
        }

        $user = User::getUserById($request->user_id);
        if ($type == 1) {
            $opengreen = getConfig('draw', '是否使用绿色积分');
            if ($opengreen != '是') {
                return $this->fail('未开启绿色积分');
            }
            $score = getConfig('draw', '消耗绿色积分');
            if ($user->green_score < $score) {
                return $this->fail('绿色积分不足');
            }

            User::score(-$score, $user->id, '抽奖消耗绿色积分', 'green_score',false);

        } elseif ($type == 2) {
            $openconfirm = getConfig('draw', '是否使用确权积分');
            if ($openconfirm != '是') {
                return $this->fail('未开启确权积分');
            }
            $score = getConfig('draw', '消耗确权积分');
            if ($user->confirm_score < $score) {
                return $this->fail('确权积分不足');
            }
            User::score(-$score, $user->id, '抽奖消耗确权积分', 'confirm_score',false);
        } else {
            return $this->fail('参数错误');
        }


        // 补充“谢谢惠顾”
        while ($prizes->count() < 8) {
            $prizes->push((object)['id' => 0, 'name' => '感谢参与']);
        }
        $randomPrize = $prizes->random();

        $UsersDrawLogData = [
            'user_id' => $user->id,
            'type' => $type,
            'score' => $score,
            'mark' => $randomPrize->name,
            'goods_id' => $randomPrize->id,
        ];
        // 随机选择一个奖品

        if ($randomPrize->id == 0) {
            // 谢谢惠顾
            $UsersDrawLogData['status'] = 0;
        } else {
            $UsersDrawLogData['status'] = 1;
        }
        UsersDrawLog::create($UsersDrawLogData);
        return $this->success('成功', $randomPrize);
    }


    function getDrawLog(Request $request)
    {
        $rows = UsersDrawLog::where('user_id', $request->user_id)->orderByDesc('id')->paginate()->items();
        return $this->success('成功', $rows);
    }


    function confirmOrder(Request $request)
    {
        $draw_id = $request->post('draw_id');
        $row = UsersDrawLog::getDrawById($draw_id);
        if ($row->status != 1) {
            return $this->fail('该奖品已领取');
        }
        $goods = ShopGoods::getGoodsById($row->goods_id);
        $sku = $goods->sku->first();
        $GoodsController = new GoodsController();
        $request->setParams('post', ['sku_id' => $sku->id, 'num' => 1,'isprize'=>1]);
        $result = $GoodsController->confirmOrder($request);
        $result = json_decode($result->rawBody());
        if ($result->code != 0) {
            return $this->fail($result->msg);
        }
        return $this->success('成功', $result->data);
    }


    function receive(Request $request)
    {
        $address_id = $request->post('address_id');
        $draw_id = $request->post('draw_id');
        $row = UsersDrawLog::getDrawById($draw_id);
        if ($row->status != 1) {
            return $this->fail('该奖品已领取');
        }
        $goods = ShopGoods::getGoodsById($row->goods_id);
        $sku = $goods->sku->first();

        $GoodsController = new GoodsController();
        $request->setParams('post',['sku_id'=>$sku->id,'num'=>1,'address_id'=>$address_id,'isprize'=>1]);
        $result = $GoodsController->pay($request);
        $result = json_decode($result->rawBody());
        if ($result->code != 0){
            return $this->fail($result->msg);
        }
        $row->status = 2;
        $row->ordersn = $result->data->ordersn;
        $row->save();
        return $this->success('成功',$result->data);
    }

    function getDrawNotice(Request $request)
    {
        $rows = UsersDrawLog::with(['user'])->orderByDesc('id')->whereIn('status',[1,2])->limit(5)->get();
        return $this->success('成功', $rows);
    }

}
