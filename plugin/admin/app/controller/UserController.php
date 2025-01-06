<?php

namespace plugin\admin\app\controller;

use plugin\admin\app\controller\Base;
use plugin\admin\app\controller\Crud;
use plugin\admin\app\model\User;
use support\exception\BusinessException;
use support\Request;
use support\Response;
use Throwable;

/**
 * 用户管理 
 */
class UserController extends Crud
{


    /**
     * @var User
     */
    protected $model = null;

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $this->model = new User;
    }

    public function select(Request $request): Response
    {
        [$where, $format, $limit, $field, $order] = $this->selectInput($request);
        $query = $this->doSelect($where, $field, $order)->with(['children']);
        return $this->doFormat($query, $format, $limit);
    }


    /**
     * 树形表格
     * @return Response
     */
    public function tree(): Response
    {
        return raw_view('user/tree');
    }


    /**
     * 浏览
     * @return Response
     * @throws Throwable
     */
    public function index(): Response
    {
        return raw_view('user/index');
    }

    /**
     * 插入
     * @param Request $request
     * @return Response
     * @throws BusinessException|Throwable
     */
    public function insert(Request $request): Response
    {
        if ($request->method() === 'POST') {
            return parent::insert($request);
        }
        return raw_view('user/insert');
    }

    /**
     * 更新
     * @param Request $request
     * @return Response
     * @throws BusinessException|Throwable
     */
    public function update(Request $request): Response
    {
        if ($request->method() === 'POST') {
            $param = $request->post();
            $user = $this->model->find($param['id']);
            if ($user->offset != $param['offset']){
                //变了账户
                $difference = $param['offset'] - $user->offset;
                User::score($difference, $user->id, $difference>0?'管理员增加':'管理员扣除','offset');
            }
            return parent::update($request);
        }
        return raw_view('user/update');
    }

}
