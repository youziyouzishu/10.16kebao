<?php

namespace app\api\controller;

use app\admin\model\UsersSignLog;
use app\api\basic\Base;
use Carbon\Carbon;
use plugin\admin\app\model\Option;
use plugin\admin\app\model\User;
use support\Request;

class SignController extends Base
{
    protected $noNeedLogin = [];

    function getConfig(Request $request)
    {
        $name = 'admin_config';
        $config = Option::where('name', $name)->value('value');
        $config = json_decode($config);
        $sign = $config->sign;
        // 获取今天的开始时间和结束时间
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();
        $row = UsersSignLog::where('user_id', $request->user_id)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->get();
        if ($row->isEmpty()) {
            $user = User::getUserById($request->user_id);
            $release_rate = $user->levelInfo->release_rate;
            $contribution_score = $release_rate == 0 ? $release_rate : round($user->green_score * $release_rate / 100, 2);
            $text = "今日预计获得{$contribution_score}贡献值";
        } else {
            $contribution_score = $row->sum('contribution_score');
            $text = "今日已获得{$contribution_score}贡献值";
        }
        $total_contribution_score = UsersSignLog::where('user_id', $request->user_id)->sum('contribution_score');
        return $this->success('成功', ['sign' => $sign, 'text' => $text, 'total_contribution_score' => $total_contribution_score]);
    }

    function doSign(Request $request)
    {
        $user = User::getUserById($request->user_id);
        $type = $request->post('type'); # 签到方式
        // 获取今天的日期范围
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $row = UsersSignLog::where('user_id', $request->user_id)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->exists();
        if ($row) {
            return $this->success('今天已经签到过了');
        }
        $contribution_score = 0;
        if ($user->green_score > 0) {
            //开始释放绿色积分 得到贡献值
            $release_rate = $user->levelInfo->release_rate;
            $contribution_score = $release_rate == 0 ? $release_rate : round($user->green_score * $release_rate / 100, 2); #释放的绿色积分
            User::score(-$contribution_score, $user->id, '签到释放绿色积分', 'green_score', false);
            User::score($contribution_score, $user->id, '签到获得贡献值', 'contribution_score', true);
            if ($user->children->isNotEmpty()){
                $children_count = $user->children->count();
                $green_score_rate = getConfig('system', '感恩释放百分比');
                $green_score = $green_score_rate == 0 ? $green_score_rate : round($contribution_score * $green_score_rate / 100, 2); #下级总释放的绿色积分
                $children_green_score = $green_score / $children_count;#下级平均释放的绿色积分
                $user->children->each(function (User $item)use($children_green_score) {
                    $release_rate = $item->levelInfo->release_rate;
                    $contribution_score = $release_rate == 0 ? $release_rate : round($children_green_score * $release_rate / 100, 2); #下级释放的绿色积分
                    if ($item->green_score <= $contribution_score){
                        $contribution_score = $item->green_score;
                    }
                    User::score(-$contribution_score, $item->id, '感恩释放', 'green_score', false);
                    User::score($contribution_score, $item->id, '感恩获得', 'contribution_score', true);
                });
            }

        }
        UsersSignLog::create([
            'user_id' => $request->user_id,
            'type' => $type,
            'green_socre' => $contribution_score,
            'contribution_score' => $contribution_score,
        ]);
        return $this->success('签到成功', ['contribution_score' => $contribution_score]);
    }


}
