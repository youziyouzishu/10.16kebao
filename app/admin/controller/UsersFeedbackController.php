<?php

namespace app\admin\controller;

use support\Request;
use support\Response;
use app\admin\model\UsersFeedback;
use plugin\admin\app\controller\Crud;
use support\exception\BusinessException;

/**
 * 反馈意见 
 */
class UsersFeedbackController extends Crud
{
    
    /**
     * @var UsersFeedback
     */
    protected $model = null;

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $this->model = new UsersFeedback;
    }
    
    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view('users-feedback/index');
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
        return view('users-feedback/insert');
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
        return view('users-feedback/update');
    }

}
