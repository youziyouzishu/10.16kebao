<?php

namespace app\api\controller;

use app\admin\model\Shop;
use app\admin\model\ShopClass;
use app\admin\model\ShopGoods;
use app\admin\model\UsersCollect;
use app\api\basic\Base;
use app\api\common\library\Pay;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\Eloquent\Builder;
use plugin\admin\app\common\Util;
use plugin\admin\app\model\Dict;
use plugin\admin\app\model\Option;
use plugin\admin\app\model\User;
use support\Request;
use support\Response;

class ShopController extends Base
{

    protected $noNeedLogin = ['getClass'];
    function getClass()
    {
        $rows = ShopClass::orderByDesc('weigh')->get();
        return $this->success('请求成功',$rows);
    }
    
    function apply(Request $request):Response
    {
        $person = $request->post('person');#法人姓名
        $mobile = $request->post('mobile');#手机号
        $name = $request->post('name');#商家名称
        $business_front = $request->post('business_front');#营业执照
        $class_id = $request->post('class_id');#主营类目
        $bank_account = $request->post('bank_account');#公司银行公户
        $province = $request->post('province');#省
        $city = $request->post('city');#市
        $region = $request->post('region');#区
        $address = $request->post('address');#详细地址
        $face_image = $request->post('face_image');#门面
        $card_front = $request->post('card_front');#法人身份证正面
        $card_reverse = $request->post('card_reverse');#法人身份证反面
        $lat = $request->post('lat');
        $lng = $request->post('lng');
        if (Shop::where(['user_id'=>$request->user_id])->whereIn('status',[2,3])->exists()){
            return $this->fail('您已经申请过了');
        }

        Shop::create([
            'user_id' => $request->user_id,
            'province' => $province,
            'city' => $city,
            'region' => $region,
            'card_front' => $card_front,
            'card_reverse' => $card_reverse,
            'class_id' => $class_id,
            'person' => $person,
            'mobile' => $mobile,
            'name' => $name,
            'address' => $address,
            'business_front' => $business_front,
            'face_image' => $face_image,
            'bank_account' => $bank_account,
            'lat'=>$lat,
            'lng'=>$lng,
            'status' => 2
        ]);
        return $this->success('申请成功');
    }

    function getConfig(Request $request)
    {
        $security_amount = getConfig('system', '商家保证金');
        return $this->success('请求成功',['security_amount'=>$security_amount]);
    }

    function applyLog(Request $request):Response
    {
        $rows = Shop::where(['user_id'=>$request->user_id])->whereNot('status',1)->orderBy('id','desc')->paginate()->items();
        return $this->success('请求成功',$rows);
    }

    function applyDetail(Request $request)
    {
        $shop_id = $request->post('shop_id');
        $shop = Shop::find($shop_id);
        return $this->success('请求成功',$shop);
    }

    function applyCancel(Request $request)
    {
        $shop_id = $request->post('shop_id');
        $shop = Shop::find($shop_id);
        if ($shop->status!=2){
            return $this->fail('状态不正确');
        }
        $shop->status = 5;
        $shop->save();
        return $this->success('请求成功');
    }

    function applyEdit(Request $request)
    {
        $param = $request->post();
        $shop = Shop::find($param['shop_id']);
        if ($shop->status != 4){
            return $this->fail('状态不正确');
        }
        $shop->fill($param);
        $shop->status = 2;
        $shop->save();
        return $this->success('请求成功');
    }

    #关注&取消关注
    function collect(Request $request)
    {
        $shop_id = $request->post('shop_id');
        $shop = Shop::getShopById($shop_id);
        $res = UsersCollect::doCollect($shop_id, $request->user_id, 'shop');
        $res ? $shop->increment('collect_num') : $shop->decrement('collect_num'); //增加/减少  收藏数量
        return $this->success('请求成功', $res);
    }

    #获取商家所有的商品分类
    function getShopClass(Request $request)
    {
        $shop_id = $request->post('shop_id');
        $shop = Shop::getShopById($shop_id);
        $goods_class = $shop->goods->pluck('class_id')->unique()->toArray();
        $class = Dict::get('goodsclass');
        $common_classes = array_filter($class, function($item) use ($goods_class) {
            return in_array($item['value'], $goods_class);
        });
        return $this->success('请求成功', $common_classes);
    }

    #列表
    function index(Request $request)
    {
        $class_id = $request->post('class_id');
        $lat = $request->post('lat');
        $lng = $request->post('lng');
        $keyword = $request->post('keyword');
        $order = $request->post('order'); # 排序类型 1=综合排序  2=离我最近  3点评高分
        $rows = Shop::where(['status' => 3])
            ->orderByDistance($lat, $lng)
            ->where('name', 'like', "%$keyword%")
            ->when(!empty($class_id), function ($query) use ($class_id) {
                $query->where(['class_id' => $class_id]);
            })

            ->when(!empty($field),function (Builder $query)use($order,$lat,$lng){
                if ($order == 1){
                    $query->orderByDesc('id');
                }
                if ($order == 2){
                    $query->orderByDesc('distance');
                }
                if ($order == 3){
                    $query->orderByDesc('rating');
                }
            })
            ->paginate()
            ->items();
        return $this->success('请求成功', $rows);
    }

    function detail(Request $request)
    {
        $shop_id = $request->post('shop_id');
        $shop = Shop::find($shop_id);
        $shop->iscollect = Shop::isCollect($request->user_id, $shop->id);
        return $this->success('请求成功',$shop);
    }

    #商家详情二维码
    function getQrcode(Request $request)
    {
        $shop_id = $request->post('shop_id');
        $shop = Shop::getShopById($shop_id);

        $data = json_encode([
            'scene'=>'shop_detail',
            'data'=>[
                'shop_id'=>$shop->id,
            ]
        ]);
        // 使用构建器创建 QR Code
        $writer = new PngWriter();
        $qrCode = new QrCode(
            data: $data,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 100,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );
        $result = $writer->write($qrCode)->getDataUri();
        return $this->success('请求成功',['base64' => $result]);


    }











}
