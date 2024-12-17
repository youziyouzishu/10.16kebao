<?php

namespace app\admin\controller;

use support\Request;
use support\Response;
use app\admin\model\DevelopScoreLog;
use plugin\admin\app\controller\Crud;
use support\exception\BusinessException;

/**
 * 拓展积分互转记录 
 */
class DevelopScoreLogController extends Crud
{
    
    /**
     * @var DevelopScoreLog
     */
    protected $model = null;

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $this->model = new DevelopScoreLog;
    }
    
    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view('develop-score-log/index');
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
        $query = $this->doSelect($where, $field, $order)->with(['user','toUser']);
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
            return parent::insert($request);
        }
        return view('develop-score-log/insert');
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
        return view('develop-score-log/update');
    }

}
