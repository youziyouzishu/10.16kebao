<?php

namespace app\admin\controller;

use app\admin\model\Area;
use support\Request;
use support\Response;
use plugin\admin\app\controller\Crud;
use support\exception\BusinessException;

/**
 * 商家列表 
 */
class AreaController extends Crud
{
    
    /**
     * @var Area
     */
    protected $model = null;


    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $this->model = new Area();
    }

    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view('area/index');
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
        return view('area/insert');
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
        return view('area/update');
    }

}
