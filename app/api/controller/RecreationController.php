<?php

namespace app\api\controller;

use app\admin\model\Recreation;
use app\api\basic\Base;
use plugin\admin\app\model\Dict;
use support\Request;

class RecreationController extends Base
{

    function getClass()
    {
        $rows = Dict::get('recreationclass');
        return $this->success('请求成功', $rows);
    }

    function index(Request $request)
    {
        $class_id = $request->post('class_id');
        $rows = Recreation::when(!empty($class_id), function ($query) use ($class_id){
            $query->where(['class_id'=>$class_id]);
        })->get();
        return $this->success('成功',$rows);
    }

}
