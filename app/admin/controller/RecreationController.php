<?php

namespace app\admin\controller;

use support\Request;
use support\Response;
use app\admin\model\Recreation;
use plugin\admin\app\controller\Crud;
use support\exception\BusinessException;

/**
 * 娱乐管理 
 */
class RecreationController extends Crud
{
    
    /**
     * @var Recreation
     */
    protected $model = null;

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $this->model = new Recreation;
    }
    
    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view('recreation/index');
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
        return view('recreation/insert');
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
        return view('recreation/update');
    }

}
