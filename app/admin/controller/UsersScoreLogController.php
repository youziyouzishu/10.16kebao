<?php

namespace app\admin\controller;

use app\admin\model\Shop;
use support\Request;
use support\Response;
use app\admin\model\UsersScoreLog;
use plugin\admin\app\controller\Crud;
use support\exception\BusinessException;

/**
 * 积分变动记录 
 */
class UsersScoreLogController extends Crud
{
    
    /**
     * @var UsersScoreLog
     */
    protected $model = null;

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $this->model = new UsersScoreLog;
    }
    
    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view('users-score-log/index');
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
        // 判断是否是商家
        if (in_array(3, admin('roles'))){
            $shop = Shop::where('admin_id',admin_id())->first();
            $where['user_id'] = $shop->user_id;
        }
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
        return view('users-score-log/insert');
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
        return view('users-score-log/update');
    }

}
