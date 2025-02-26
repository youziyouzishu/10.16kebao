<?php

namespace process;

use app\admin\model\PieceLog;
use app\admin\model\ShopGoodsOrders;
use app\admin\model\Statement;
use Carbon\Carbon;
use plugin\admin\app\model\User;
use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {


        //每天凌晨执行 统计销售额
        new Crontab('30 0 1-31/2 * *', function () {
            $now = Carbon::now();
            $orders_amount = ShopGoodsOrders::whereIn('status', [10, 11])->where('created_at', '<=', $now)->sum('should_pay_amount');
            dump($orders_amount);
            $piece_amount = PieceLog::where('created_at', '<=', $now)->sum('pay_amount');
            dump($piece_amount);
            $sale_amount = $orders_amount + $piece_amount;
            dump('销售额:'.$sale_amount);
            $row = Statement::orderByDesc('id')->first();
            if ($row) {
                $increase_rate = round(($sale_amount - $row->sale_amount) / $row->sale_amount * 100, 2);
//            if ($increase_rate > 15){
//                $increase_rate = 15;
//            }
                dump('增长率:'.$increase_rate);
            } else {
                $increase_rate = 0;
            }
            Statement::create([
                'sale_amount' => $sale_amount,
                'statistics_date' => Carbon::yesterday()->toDateString(),
                'increase_rate' => $increase_rate
            ]);

            $rows = ShopGoodsOrders::where('await_green_score', '>', 0)->withCount('dividends')->having('dividends_count', '<', 36)->get();

            $rows->each(function (ShopGoodsOrders $item) use ($increase_rate) {
                if ($item->dividends->isEmpty()) {
                    //第一次释放
                    $get_coupon_score = round($item->get_green_score * 0.005, 2);#获得消费券
                    $expend_green_score = round($item->get_green_score * 0.001, 2);#释放绿色积分
                } else {
                    $last = $item->dividends()->orderByDesc('id')->first();
                    $get_coupon_score = round($last->get_coupon_score * (1 + ($increase_rate / 100)), 2);
                    $expend_green_score = round($get_coupon_score / 5, 2);
                }
                if ($item->user->green_score >= $expend_green_score) {
                    $item->await_green_score -= $expend_green_score;
                    $item->save();
                    $item->dividends()->create([
                        'get_coupon_score' => $get_coupon_score,
                        'expend_green_score' => $expend_green_score,
                        'user_id' => $item->user_id,
                    ]);
                    User::score(-$expend_green_score, $item->user_id, '释放绿色积分获得消费券', 'green_score');
                    User::score($get_coupon_score, $item->user_id, '释放绿色积分获得消费券', 'coupon_score');
                }
            });

        });


    }
}