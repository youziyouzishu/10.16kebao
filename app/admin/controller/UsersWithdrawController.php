<?php

namespace app\admin\controller;

use app\admin\model\Shop;
use plugin\admin\app\model\User;
use support\Request;
use support\Response;
use app\admin\model\UsersWithdraw;
use plugin\admin\app\controller\Crud;
use support\exception\BusinessException;

/**
 * 提现记录
 */
class UsersWithdrawController extends Crud
{

    /**
     * @var UsersWithdraw
     */
    protected $model = null;

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $this->model = new UsersWithdraw;
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
        if (in_array(3, admin('roles'))) {
            //商家
            $shop = Shop::where('admin_id', admin_id())->first();
            $where['user_id'] = $shop->user_id;
        }
        $query = $this->doSelect($where, $field, $order);

        return $this->doFormat($query, $format, $limit);
    }

    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        $admin_id = admin_id();
        $shop = Shop::where('admin_id', $admin_id)->first();

        if (!$shop) {
            $money = User::sum('money');
        }else{
            $money = $shop->user->money;
        }
        return view('users-withdraw/index',['money'=>$money]);
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
            $admin_id = admin_id();
            $shop = Shop::where('admin_id', $admin_id)->first();
            if (!$shop) {
                return $this->fail('非商家不能提现');
            }
            $withdraw_amount = $request->post('withdraw_amount');
            if ($shop->user->money < $withdraw_amount) {
                return $this->fail('余额不足');
            }
            User::score(-$withdraw_amount, $shop->user_id, '商家提现', 'money');
            return parent::insert($request);
        }
        return view('users-withdraw/insert');
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
            $id = $request->post('id');
            $param = $request->post();
            $row = $this->model->find($id);
            if (!$row) {
                return $this->fail('记录不存在');
            }
            if ($row->status == 0 && $param['status'] == 2) {
                //驳回 返回余额
                User::score($row->withdraw_amount, $row->user_id, '商家提现驳回', 'money');
            }
            return parent::update($request);
        }
        return view('users-withdraw/update');
    }

}
