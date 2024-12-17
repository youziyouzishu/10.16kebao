<?php

namespace app\admin\controller;

use app\admin\model\Shop;
use support\Request;
use support\Response;
use app\admin\model\ShopGoodsOrders;
use plugin\admin\app\controller\Crud;
use support\exception\BusinessException;

/**
 * 商家订单记录 
 */
class ShopGoodsOrdersController extends Crud
{
    
    /**
     * @var ShopGoodsOrders
     */
    protected $model = null;

    /**
     * 查询
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function select(Request $request): Response
    {

        [$where, $format, $limit, $field, $order] = $this->selectInput($request);
        // 判断是否是商家
        if (in_array(3,admin('roles'))){
            $where['admin_id'] = admin_id();
        }
        $query = $this->doSelect($where, $field, $order)->with(['user','shop','goods','sku','address']);
        return $this->doFormat($query, $format, $limit);
    }

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $this->model = new ShopGoodsOrders;
    }
    
    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        $shop = Shop::where('admin_id', admin_id())->first();
        return view('shop-goods-orders/index',['shop'=>$shop]);
    }

    /**
     * 插入
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert(Request $request): Response
    {
        if ($request->method() === 'POST') {
            return parent::insert($request);
        }
        return view('shop-goods-orders/insert');
    }

    /**
     * 更新
     * @param Request $request
     * @return Response
     * @throws BusinessException
    */
    public function update(Request $request): Response
    {
        if ($request->method() === 'POST') {
            $param = $request->post();
            $row = $this->model->find($param['id']);

            if ($row->status == 6&&$param['status']==7 ){
                //收到货了  开始退款
                //执行退款
            }
            return parent::update($request);
        }
        return view('shop-goods-orders/update');
    }

}
