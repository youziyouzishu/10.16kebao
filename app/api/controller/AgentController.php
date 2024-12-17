<?php

namespace app\api\controller;

use app\admin\model\Agent;
use app\admin\model\Consumer;
use app\api\basic\Base;
use plugin\admin\app\model\Option;
use support\Request;

class AgentController extends Base
{
    #成为券商
    function apply(Request $request)
    {
        $type = $request->post('type');
        if ($type == 1){
            $agent = Agent::where(['user_id' => $request->user_id])->whereIn('status', [0, 1])->exists();
            if ($agent) {
                return $this->fail('您已经申请过了');
            }
            Agent::create([
                'user_id' => $request->user_id,
            ]);
        }else{
            $agent = Consumer::where(['user_id' => $request->user_id])->whereIn('status', [0, 1])->exists();
            if ($agent) {
                return $this->fail('您已经申请过了');
            }
            Consumer::create([
                'user_id' => $request->user_id,
            ]);
        }

        return $this->success('提交成功');
    }



    function cancelAgent(Request $request)
    {
        $agent_id = $request->post('agent_id');
        $row = Agent::find($agent_id);
        if ($row->status != 0) {
            return $this->fail('状态异常');
        }
        $row->status = 3;
        $row->save();
        return $this->success('取消成功');
    }

    function getApplyDetail(Request $request)
    {
        $agent_id = $request->post('agent_id');
        $row = Agent::find($agent_id);
        return $this->success('请求成功', $row);
    }

    function getApplyList(Request $request)
    {
        $type = $request->post('type');
        if ($type == 1){
            $rows = Agent::where(['user_id' => $request->user_id])->orderByDesc('id')->get();

        }else{
            $rows = Consumer::where(['user_id' => $request->user_id])->orderByDesc('id')->get();
        }

        return $this->success('请求成功', $rows);
    }

    function getAgentList(Request $request)
    {
        $level = $request->post('level');
        $rows = Agent::where(['status' => 1, 'level' => $level])->get();
        return $this->success('请求成功', $rows);
    }

    function getConfig(Request $request)
    {
        $name = 'admin_config';
        $config = Option::where('name', $name)->value('value');
        $config =  json_decode($config);
        $agent = $config->agent;
        return $this->success('成功', ['agent' => $agent]);
    }

}
