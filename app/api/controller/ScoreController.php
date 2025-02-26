<?php

namespace app\api\controller;

use app\admin\model\UsersScoreLog;
use app\api\basic\Base;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use plugin\admin\app\model\Option;
use plugin\admin\app\model\User;
use support\Request;

class ScoreController extends Base
{
    #获取积分记录
    function getUserScoreLog(Request $request)
    {
        # green_score=绿色积分,
        # coupon_score=消费券,
        # offset=抵用券,
        $type = $request->post('type');
        $month = $request->post('month');

        $date = Carbon::parse($month);
        // 提取年份和月份
        $year = $date->year;
        $month = $date->month;
        $status = $request->post('status'); #0=全部 1=支出，2=收入
        $rows = UsersScoreLog::where(['type' => $type])
            ->when(!empty($status), function (Builder $query) use ($status) {
                if ($status == 1) {
                    $query->where('score', '<', 0);
                } else {
                    $query->where('score', '>', 0);
                }
            })
            ->whereYear('created_at',$year)
            ->whereMonth('created_at',$month)
            ->where('user_id', $request->user_id)
            ->orderByDesc('id')
            ->paginate()
            ->getCollection()
            ->each(function ($item) {
                if ($item->score > 0) {
                    $item->score = '+' . $item->score;
                }
            });
        return $this->success('获取成功', $rows);
    }


    #抵扣券转消费券
    function offsetToCoupon(Request $request)
    {
        $offset = $request->post('offset');
        $user = User::find($request->user_id);
        if ($user->offset < $offset){
            return $this->fail('抵扣券不足');
        }
        $name = 'admin_config';
        $config = Option::where('name', $name)->value('value');
        $config = json_decode($config);
        $offset_rate = $config->offset_rate;
        $coupon_score = $offset * $offset_rate / 100;
        User::score(-$offset, $request->user_id, '抵扣券转消费券', 'offset');
        User::score($coupon_score, $request->user_id, '抵扣券转消费券', 'coupon_score');
        return $this->success('成功');
    }

    #转赠抵用券
    function transfer(Request $request)
    {
        $offset = $request->post('offset');
        $to_user_id = $request->post('to_user_id');
        $user = User::find($request->user_id);
        if ($user->id == $to_user_id){
            return $this->fail('不能转给自己');
        }
        if (empty($user->agent)){
            return $this->fail('请先成为券商');
        }
        if ($user->offset < $offset){
            return $this->fail('抵用券不足');
        }
        User::score(-$offset, $request->user_id, '转赠抵用券', 'offset');
        User::score($offset, $to_user_id, '收到抵用券', 'offset');
        return $this->success('成功');
    }

}
