<?php

namespace process;

use Carbon\Carbon;
use plugin\admin\app\model\User;
use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {

        // 每天的7点50执行，注意这里省略了秒位
        new Crontab('50 7 * * *', function () {
            #获取绿色积分大于0的所有用户
            $users = User::where('green_score', '>', 0)->get();
            $days = getConfig('system', '分享释放天数');
            $green_rate = getConfig('system', '分享释放百分比');
            // 使用 Carbon 获取当前时间并减去指定天数
            $cutoffDate = Carbon::now()->subDays($days);
            $users->each(function (User $user) use ($cutoffDate, $green_rate) {
                //如果有下级 获取符合日期并且积分大于0的下级
                $children = $user->children()->where('created_at', '>', $cutoffDate)->where('green_score', '>', 0)->get();
                if ($children->isNotEmpty()) {
                    $contribution_score = 0;
                    $children->each(function (User $item) use (&$contribution_score, $green_rate) {
                        $green_score = $green_rate == 0 ? $green_rate : round($item->green_score * $green_rate / 100, 2); #下级释放的绿色积分
                        $contribution_score += $green_score;
                    });
                    if ($user->green_score <= $contribution_score) {
                        $contribution_score = $user->green_score;
                    }
                    $release_rate = $user->levelInfo->release_rate;
                    $contribution_score = $release_rate == 0 ? $release_rate : round($contribution_score * $release_rate / 100, 2); #释放的绿色积分
                    User::score(-$contribution_score, $user->id, '分享释放', 'green_score', );
                    User::score($contribution_score, $user->id, '分享获得', 'contribution_score' );
                }
            });
        });

    }
}