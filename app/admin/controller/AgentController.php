<?php

namespace app\admin\controller;

use plugin\admin\app\model\User;
use support\Request;
use support\Response;
use app\admin\model\Agent;
use plugin\admin\app\controller\Crud;
use support\exception\BusinessException;

/**
 * 代理列表 
 */
class AgentController extends Crud
{
    
    /**
     * @var Agent
     */
    protected $model = null;

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $this->model = new Agent;
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
        $query = $this->doSelect($where, $field, $order)->with(['user']);
        return $this->doFormat($query, $format, $limit);
    }
    
    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view('agent/index');
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
        return view('agent/insert');
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
        return view('agent/update');
    }

}
