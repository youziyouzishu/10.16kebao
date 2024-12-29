<?php

namespace app\api\controller;

use app\admin\model\GoodsClass;
use app\admin\model\Shop;
use app\admin\model\ShopCar;
use app\admin\model\ShopGoods;
use app\admin\model\ShopGoodsOrders;
use app\admin\model\ShopGoodsSku;
use app\admin\model\UsersCollect;
use app\api\basic\Base;
use EasyWeChat\MiniApp\Application;
use Illuminate\Database\Eloquent\Builder;
use plugin\admin\app\common\Util;
use plugin\admin\app\model\User;
use support\Request;
use Webman\RedisQueue\Client;

class GoodsController extends Base
{
    protected $noNeedLogin = ['getClass','confirmOrder'];
    function getClass()
    {
        $rows = GoodsClass::orderByDesc('weigh')->get();
        return $this->success('请求成功', $rows);
    }

    function goodsList(Request $request)
    {
        $shop_id = $request->post('shop_id');
        $class_id = $request->post('class_id');
        $keyword = $request->post('keyword');
        $field = $request->post('field', 'id');
        $order = $request->post('order', 'desc');
        $rows = ShopGoods::where(['status' => 1])
            ->with(['shop'])
            ->when(!empty($shop_id), function (Builder $query) use ($shop_id) {
                $query->where('shop_id', $shop_id);
            })
            ->when(!empty($keyword), function (Builder $query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->when(!empty($class_id), function ($query) use ($class_id) {
                $query->where('class_id', $class_id);
            })
            ->orderBy($field, $order)
            ->withMin('sku', 'price')
            ->orderByDesc('id')
            ->paginate()
            ->items();

        return $this->success('请求成功', $rows);
    }

    function detail(Request $request)
    {
        $goods_id = $request->post('goods_id');
        $row = ShopGoods::getGoodsById($goods_id);
        $assess = [];
        $assess['list'] = $row->assess()->with(['sku', 'user'])->orderByDesc('id')->limit(2)->get();
        $assess['statistics'] = [
            'good' => $row->assess()->where('rating', '>=', 4)->count(),
            'normal' => $row->assess()->where('rating', '>=', 3)->where('rating', '<', 4)->count(),
            'bad' => $row->assess()->where('rating', '<', 3)->count(),
            'has_image' => $row->assess()->whereNotNull('images')->count(),
        ];
        $row->assess_info = $assess;
        $row->load(['sku', 'shop'=>function ($query) {
            $query->with(['topGoods']);
        }]);
        $row->setAttribute('iscollect',ShopGoods::isCollect($request->user_id, $goods_id));
        $app = new Application(config('wechat'));
        $data = [
            'scene' => $goods_id,
            'page' => 'pages/good/detail',
            'width' => 280,
            'check_path' => !config('app.debug'),
        ];
        $response = $app->getClient()->postJson('/wxa/getwxacodeunlimit', $data);
        $base64 = "data:image/png;base64,".base64_encode($response->getContent());
        $row->setAttribute('base64',$base64);

        return $this->success('请求成功', $row);
    }

    function getAssessList(Request $request)
    {
        $goods_id = $request->post('goods_id');
        $row = ShopGoods::getGoodsById($goods_id);
        $assess['list'] = $row->assess()->with(['sku', 'user'])->orderByDesc('id')->paginate()->items();
        $assess['statistics'] = [
            'good' => $row->assess()->where('rating', '>=', 4)->count(),
            'normal' => $row->assess()->where('rating', '>=', 3)->where('rating', '<', 4)->count(),
            'bad' => $row->assess()->where('rating', '<', 3)->count(),
            'has_image' => $row->assess()->whereNotNull('images')->count(),
        ];
        return $this->success('请求成功', $assess);
    }


    #关注&取消关注
    function collect(Request $request)
    {
        $goods_id = $request->post('goods_id');
        $goods = ShopGoods::getGoodsById($goods_id);
        $res = UsersCollect::doCollect($goods_id, $request->user_id, 'goods');
        $res ? $goods->increment('collect_num') : $goods->decrement('collect_num'); //增加/减少  收藏数量
        return $this->success('请求成功', $res);
    }

    function shopCarAdd(Request $request)
    {
        $sku_id = $request->post('sku_id');
        $num = $request->post('num', 1);
        $tag = $request->post('tag');
        $sku = ShopGoodsSku::getSkuById($sku_id);
        if ($sku->num < $num) {
            return $this->fail('库存不足');
        }
        $shopcar = ShopCar::where(['user_id' => $request->user_id, 'sku_id' => $sku_id,'tag' => $tag])->first();
        if ($shopcar) {
            $shopcar->increment('num');
        } else {
            ShopCar::create([
                'user_id' => $request->user_id,
                'sku_id' => $sku_id,
                'num' => $num,
                'shop_id' => $sku->goods->shop_id,
                'goods_id' => $sku->goods_id,
                'tag' => $tag
            ]);
        }
        return $this->success('请求成功');
    }

    function shopCarList(Request $request)
    {
        $shop_ids = ShopCar::where(['user_id' => $request->user_id])->pluck('shop_id')->unique();
        $rows = Shop::whereIn('id', $shop_ids)
            ->select('id', 'name')
            ->get()
            ->each(function (Shop $item)use($request) {
                $sku_goods = ShopCar::with(['goods.sku', 'sku'])->where(['shop_id'=> $item->id,'user_id'=>$request->user_id])->get();
                $item->setAttribute('sku_goods', $sku_goods);
            });
        return $this->success('请求成功', $rows);
    }

    function shopCarEdit(Request $request)
    {
        $shopcar = $request->post('shopcar');//[{"id":"xx","num":"xx"}]
        foreach ($shopcar as $item) {
            $shopcar = ShopCar::getShopCarById($item['id']);
            $shopcar->num = $item['num'];
            $shopcar->save();
        }
        return $this->success('请求成功');
    }

    function shopCarDelete(Request $request)
    {
        $shopcar_ids = $request->post('shopcar_ids');
        $shopcar_ids = explode(',', $shopcar_ids);
        ShopCar::whereIn('id', $shopcar_ids)->delete();
        return $this->success('请求成功');
    }


    function confirmOrder(Request $request)
    {
        $sku = $request->post('sku');//[{"id":"xxxx","num":"xxxx","tag":"xxxx"}]
        $freight = 0;
        $goods_amount = 0;
        $num = 0;
        $list = [];
        foreach ($sku as $item) {
            $sku = ShopGoodsSku::getSkuById($item['id']);
            $sku->setAttribute('tag',$item['tag']);
            $sku->setAttribute('buy_num',$item['num']);
            $sku->load(['goods']);
            $list[] = $sku;
            $freight += $sku->goods->freight;
            $goods_amount += $sku->price * $item['num'];
            $num += $item['num'];
        }
        $pay_amount = $goods_amount + $freight;

        $data = [
            'list'=>$list,
            'num' => $num,
            'freight' => $freight,
            'goods_amount' => $goods_amount,
            'pay_amount' => $pay_amount,
        ];
        return $this->success('请求成功', $data);
    }

    function pay(Request $request)
    {
        dump($request->post());
        $use_coupon_score = $request->post('use_coupon_score'); #是否使用 优惠券额度 0否  1是
        $address_id = $request->post('address_id');
        $mark = $request->post('mark', '');
        $sku = $request->post('sku');//[{"id":"xxxx","tag":"xxxx","num":"xxxx"}]
        $user = User::getUserById($request->user_id);
        foreach ($sku as $item) {
            $sku = ShopGoodsSku::getSkuById($item['id']);
            $deduction_coupon_score = 0;
            if ($use_coupon_score == 1) {
                if ($sku->price * $item['num'] + $sku->goods->freight > $user->coupon_score) {
                    $deduction_coupon_score = $user->coupon_score;
                } else {
                    $deduction_coupon_score = $sku->price * $item['num'] + $sku->goods->freight;
                }
                User::score(-$deduction_coupon_score, $user->id, '抵扣优惠券额度', 'coupon_score', false);
            }
            $ordersn = Util::generateOrdersn();
            $ordersData = [
                'admin_id'=>$sku->goods->shop->admin_id,
                'ordersn' => $ordersn,
                'user_id' => $request->user_id,
                'shop_id' => $sku->goods->shop_id,
                'pay_amount' => $sku->price * $item['num'] + $sku->goods->freight,
                'sku_id' => $sku->id,
                'goods_id' => $sku->goods_id,
                'num' => $item['num'],
                'freight' => $sku->goods->freight,
                'goods_amount' => $sku->price * $item['num'],
                'address_id' => $address_id,
                'mark' => $mark,
                'tag' => $item['tag'],
                'deduction_coupon_score' => $deduction_coupon_score,
            ];
            $order = ShopGoodsOrders::create($ordersData);

            if ($sku->price * $item['num'] + $sku->goods->freight - $deduction_coupon_score == 0) {
                $notify = new NotifyController();
                $request->setParams('get', ['paytype' => 'balance', 'out_trade_no' => $ordersn, 'attach' => 'goods']);
                $res = $notify->balance($request);
                $res = json_decode($res->rawBody());
                if ($res->code == 1) {
                    return $this->fail($res->msg);
                }
                return $this->success('请求成功');
            } else {
                // 队列名
                $queue = 'order';
                // 数据，可以直接传数组，无需序列化
                $data = ['order_id' => $order->id, 'event' => 'order_expire'];
                Client::send($queue, $data, 60 * 15);
            }

            if ($car = ShopCar::where(['sku_id' => $item['id'], 'user_id' => $request->user_id])->first()) {
                $car->delete();
            }
        }
        return $this->success('请求成功');

    }


}
