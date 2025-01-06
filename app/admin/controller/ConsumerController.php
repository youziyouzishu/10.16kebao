<?php

namespace app\admin\controller;

use plugin\admin\app\common\Util;
use plugin\admin\app\model\Admin;
use plugin\admin\app\model\AdminRole;
use support\Request;
use support\Response;
use app\admin\model\Consumer;
use plugin\admin\app\controller\Crud;
use support\exception\BusinessException;

/**
 * 消费商列表 
 */
class ConsumerController extends Crud
{
    
    /**
     * @var Consumer
     */
    protected $model = null;

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $this->model = new Consumer;
    }
    
    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view('consumer/index');
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
        return view('consumer/insert');
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

            $row = $this->model->find($param['id']);
            if ($row->status == 0 && $param['status'] == 1) {
                //审核通过 增加管理员
                $admin = new Admin;
                $admin->username = $row->user->username;
                $admin->nickname = $row->user->nickname;
                $admin->password = Util::passwordHash('123456');
                $admin->avatar = $row->user->avatar;
                $admin->mobile = $row->user->mobile;
                $admin->save();
                $admin_roles = new AdminRole;
                $admin_roles->role_id = 4;
                $admin_roles->admin_id = $admin->id;
                $admin_roles->save();
                $request->setParams('post', ['admin_id' => $admin->id]);
            }
            return parent::update($request);
        }
        return view('consumer/update');
    }

}
