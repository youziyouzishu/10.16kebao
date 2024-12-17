<?php

namespace app\admin\controller;

use support\Request;
use support\Response;
use app\admin\model\ShopGoodsOrdersAssess;
use plugin\admin\app\controller\Crud;
use support\exception\BusinessException;

/**
 * 订单评价记录 
 */
class ShopGoodsOrdersAssessController extends Crud
{
    
    /**
     * @var ShopGoodsOrdersAssess
     */
    protected $model = null;

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $this->model = new ShopGoodsOrdersAssess;
    }
    
    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view('shop-goods-orders-assess/index');
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
        return view('shop-goods-orders-assess/insert');
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
            return parent::update($request);
        }
        return view('shop-goods-orders-assess/update');
    }

}
