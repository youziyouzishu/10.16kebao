<?php

namespace app\admin\controller;

use support\Request;
use support\Response;
use app\admin\model\Level;
use plugin\admin\app\controller\Crud;
use support\exception\BusinessException;

/**
 * 等级管理 
 */
class LevelController extends Crud
{
    
    /**
     * @var Level
     */
    protected $model = null;

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $this->model = new Level;
    }
    
    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view('level/index');
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
        return view('level/insert');
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
        return view('level/update');
    }

}
