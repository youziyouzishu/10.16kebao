<?php

namespace app\admin\controller;

use support\Request;
use support\Response;
use app\admin\model\AwaitConfirmScoreLog;
use plugin\admin\app\controller\Crud;
use support\exception\BusinessException;

/**
 * 待确权权益兑换记录 
 */
class AwaitConfirmScoreLogController extends Crud
{
    
    /**
     * @var AwaitConfirmScoreLog
     */
    protected $model = null;

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $this->model = new AwaitConfirmScoreLog;
    }
    
    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view('await-confirm-score-log/index');
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
        return view('await-confirm-score-log/insert');
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
        return view('await-confirm-score-log/update');
    }

}
