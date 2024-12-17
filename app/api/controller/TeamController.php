<?php

namespace app\api\controller;

use app\api\basic\Base;
use plugin\admin\app\model\Option;
use support\Request;

class TeamController extends Base
{
    function getConfig(Request $request)
    {
        $name = 'admin_config';
        $config = Option::where('name', $name)->value('value');
        $config =  json_decode($config);
        $team = $config->team;
        return $this->success('成功', ['team' => $team]);
    }

}
