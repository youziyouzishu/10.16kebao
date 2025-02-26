<?php

namespace app\api\common\library;

use Psr\Http\Message\MessageInterface;
use support\exception\BusinessException;
use Yansongda\Artful\Exception\ContainerException;
use Yansongda\Artful\Rocket;
use Yansongda\Pay\Plugin\Wechat\V3\Pay\Combine\MiniPayPlugin;
use Yansongda\Supports\Collection;

class Pay
{


    /**
     * 支付
     * @param $pay_type *支付类型:1=微信,2=支付宝
     * @param  $pay_amount
     * @param  $order_no
     * @param $mark
     * @param $attach
     * @param string $openid
     * @return Rocket|Collection
     */
    public static function pay($pay_type,$pay_amount, $order_no, $mark, $attach, string $openid = '')
    {
        $config = config('payment');
        \Yansongda\Pay\Pay::config($config);
        try {
            if ($pay_type == 1){
                $result = \Yansongda\Pay\Pay::wechat()->mini([
                    'out_trade_no' => $order_no,
                    'description' => $mark,
                    'amount' => [
                        'total' => function_exists('bcmul') ? (int)bcmul($pay_amount, 100, 2) : $pay_amount * 100,
                        'currency' => 'CNY',
                    ],
                    'payer' => [
                        'openid' => $openid,
                    ],
                    'attach' => $attach
                ]);
            }elseif($pay_type == 2){

            }else{
                throw new BusinessException('支付类型错误',1);
            }
            return $result;
        }catch (\Throwable $e){
            throw new BusinessException($e->getMessage(),1);
        }


    }

    /**
     * 合单支付
     * @param $pay_type *支付类型:1=微信,2=支付宝
     * @param array $orderinfo
     * @param $combine_out_trade_no
     * @param string $openid
     * @return MessageInterface|Rocket|Collection|null
     * @throws BusinessException
     * @throws ContainerException
     */
    public static function combinePay($pay_type, array $orderinfo, $combine_out_trade_no,string $openid = '')
    {
        $config = config('payment');
        \Yansongda\Pay\Pay::config($config);
        try {
            if ($pay_type == 1){
                $allPlugins = \Yansongda\Pay\Pay::wechat()->mergeCommonPlugins([MiniPayPlugin::class]);
                $sub_orders = [];
                foreach ($orderinfo as $item){
                    $sub_orders[] = [
                        'mchid'=>config('payment.wechat.default.mch_id'),
                        'attach'=>$item['attach'],
                        'amount' => [
                            'total_amount' => (int)bcmul($item['pay_amount'], 100, 2),
                            'currency' => 'CNY',
                        ],
                        'out_trade_no'=>$item['ordersn'],
                        'description'=>$item['description']
                    ];
                }
                $result = \Yansongda\Pay\Pay::wechat()->pay($allPlugins, [
                    'combine_out_trade_no'=>$combine_out_trade_no,
                    'sub_orders'=>$sub_orders,
                    'combine_payer_info'=>[
                        'openid' =>$openid
                    ]
                ]);
            }elseif($pay_type == 2){

            }else{
                throw new BusinessException('支付类型错误',1);
            }
            return $result;
        }catch (\Throwable $e){
            throw new BusinessException($e->getMessage(),1);
        }

    }

    #退款
    public static function refund($pay_type, $pay_amount, $order_no, $refund_order_no, $reason)
    {
        $config = config('payment');
        return match ($pay_type) {
            1 => \Yansongda\Pay\Pay::wechat($config)->refund([
                'out_trade_no' => $order_no,
                'out_refund_no' => $refund_order_no,
                'amount' => [
                    'refund' => (int)bcmul($pay_amount, 100, 2),
                    'total' => (int)bcmul($pay_amount, 100, 2),
                    'currency' => 'CNY',
                ],
                'reason' => $reason
            ]),
            default => throw new \Exception('支付类型错误'),
        };
    }
}