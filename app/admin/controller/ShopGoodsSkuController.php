<?php

namespace app\admin\controller;

use app\admin\model\ShopGoods;
use support\Request;
use support\Response;
use app\admin\model\ShopGoodsSku;
use plugin\admin\app\controller\Crud;
use support\exception\BusinessException;

/**
 * 商品规格 
 */
class ShopGoodsSkuController extends Crud
{
    
    /**
     * @var ShopGoodsSku
     */
    protected $model = null;

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $this->model = new ShopGoodsSku;
    }
    
    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view('shop-goods-sku/index');
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
        $query = $this->doSelect($where, $field, $order);
        return $this->doFormat($query, $format, $limit);
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
            $param = $request->post();
            $goods = ShopGoods::find($param['goods_id']);

            if (!$goods){
                return $this->fail('商品不存在');
            }
            if ($goods->type == 1){
                return $this->fail('拼夺商品不允许添加规格');
            }
            if ($param['price'] <=0){
                return $this->fail('商品价格不能小于0');
            }
            if ($param['total'] <= 0){
                return $this->fail('商品库存不能小于0');
            }
            $request->setParams('post',[
                'num'=>$request->post('total')
            ]);

            return parent::insert($request);
        }
        return view('shop-goods-sku/insert');
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
            $goods = ShopGoods::find($param('goods_id'));
            if (!$goods){
                return $this->fail('商品不存在');
            }
            if ($goods->type == 1){
                return $this->fail('拼夺商品不允许添加规格');
            }
            if ($param['price'] <=0 ){
                return $this->fail('商品价格不能小于0');
            }
            if ($param['total'] <= 0){
                return $this->fail('商品库存不能小于0');
            }
            $request->setParams('post',[
                'num'=>$request->post('total')
            ]);
            return parent::update($request);
        }
        return view('shop-goods-sku/update');
    }

}
