<?php

namespace app\admin\controller;

use app\admin\model\Shop;
use plugin\admin\app\model\Admin;
use support\Request;
use support\Response;
use app\admin\model\ShopGoods;
use plugin\admin\app\controller\Crud;
use support\exception\BusinessException;

/**
 * 商品列表 
 */
class ShopGoodsController extends Crud
{
    
    /**
     * @var ShopGoods
     */
    protected $model = null;

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $this->model = new ShopGoods;
    }

    public function select(Request $request): Response
    {

        [$where, $format, $limit, $field, $order] = $this->selectInput($request);
        // 判断是否是商家
        if (in_array(3, admin('roles'))){
            $where['admin_id'] = admin_id();
        }
        $query = $this->doSelect($where, $field, $order);
        return $this->doFormat($query, $format, $limit);
    }
    
    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view('shop-goods/index');
    }

    /**
     * 插入
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert(Request $request): Response
    {
        $shop = Shop::with(['user'])->where('admin_id',admin_id())->first();
        if ($request->method() === 'POST') {
            $param = $request->post();
            if (!$shop){
                return $this->fail('非商家不能操作');
            }
            if ($param['type'] == 1){
                if ($shop->user->type == 0){
                    return $this->fail('非官方不能操作');
                }

                if ($param['coupon_zone']==0){
                    return $this->fail('请选择券区');
                }
                if ($param['piece_type']==0){
                    return $this->fail('请选择拼夺类型');
                }
                if ($param['total']==0){
                    return $this->fail('拼夺总数量不能为0');
                }
                if ($param['get_coupon_score']==0){
                    return $this->fail('拼夺消费券不能为0');
                }
            }
            $request->setParams('post',[
                'user_id'=> $shop->user_id,
                'shop_id'=> $shop->id,
                'admin_id'=> admin_id(),
                'num'=>$param['total']
            ]);

            return parent::insert($request);
        }

        return view('shop-goods/insert',['shop'=>$shop]);
    }

    /**
     * 更新
     * @param Request $request
     * @return Response
     * @throws BusinessException
    */
    public function update(Request $request): Response
    {
        $shop = Shop::with(['user'])->where('admin_id',admin_id())->first();

        if ($request->method() === 'POST') {
            $param = $request->post();
            if (!$shop){
                return $this->fail('非商家不能操作');
            }
            if ($param['type'] == 1){
                if ($shop->user->type == 0){
                    return $this->fail('非官方不能操作');
                }
                if ($param['coupon_zone']==0){
                    return $this->fail('请选择券区');
                }
                if ($param['piece_type']==0){
                    return $this->fail('请选择拼夺类型');
                }
                if ($param['total']==0){
                    return $this->fail('拼夺总数量不能为0');
                }
                if ($param['get_coupon_score']==0){
                    return $this->fail('拼夺消费券不能为0');
                }
            }
            $request->setParams('post',[
                'num'=>$param['total']
            ]);
            return parent::update($request);
        }
        return view('shop-goods/update',['shop'=>$shop]);
    }

}
