<?php

namespace app\admin\controller;

use support\Request;
use support\Response;
use app\admin\model\ShopGoodsOrdersAfter;
use plugin\admin\app\controller\Crud;
use support\exception\BusinessException;

/**
 * 售后记录 
 */
class ShopGoodsOrdersAfterController extends Crud
{
    
    /**
     * @var ShopGoodsOrdersAfter
     */
    protected $model = null;

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $this->model = new ShopGoodsOrdersAfter;
    }

    /**
     * 查询
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function select(Request $request): Response
    {
        [$where, $format, $limit, $field, $order] = $this->selectInput($request);
        $query = $this->doSelect($where, $field, $order)->with(['user','orders']);
        return $this->doFormat($query, $format, $limit);
    }
    
    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view('shop-goods-orders-after/index');
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
        return view('shop-goods-orders-after/insert');
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
            if($row->status == 0){
                if ($param['status'] == 1){
                    //同意售后
                    if ($row->orders->status == 4){
                        //订单状态是申请售后中
                        if ($row->type == 0){
                            //退货退款
                            $row->orders->status = 5;
                        }else{
                            //我要退款
                            $row->orders->status = 7;
                            //执行退款
                        }
                        $row->orders->save();
                    }
                }elseif ($param['status'] == 2){
                    //拒绝售后
                    if ($row->orders->status == 4) {
                        //订单状态是申请售后中
                        $row->orders->status = $row->orders->after_before_status;
                        $row->orders->save();
                    }
                }
            }
            return parent::update($request);
        }
        return view('shop-goods-orders-after/update');
    }

}
