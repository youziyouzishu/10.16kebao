<?php

namespace app\api\controller;

use app\admin\model\UsersBankCard;
use app\api\basic\Base;
use plugin\admin\app\common\Util;
use plugin\admin\app\model\Dict;
use support\Request;

class BankController extends Base
{
    function getBankList(Request $request)
    {
        $rows = Dict::get('bank');
        return $this->success('成功',$rows);
    }

    function getUserBankCardList(Request $request)
    {
        $rows = UsersBankCard::where('user_id',$request->user_id)->get();
        return $this->success('成功',$rows);
    }

    function UserBankCardAdd(Request $request)
    {
        $bank_id = $request->post('bank_id');
        $truename = $request->post('truename');
        $cardnum = $request->post('cardnum');
        $mobile = $request->post('mobile');
        $bank_name = Util::getDictNameByValue('bank',$bank_id);

        $user_bank_card = UsersBankCard::where(['user_id'=>$request->user_id,'cardnum'=>$cardnum])->first();
        if ($user_bank_card){
            return $this->fail('该银行卡已绑定');
        }

        UsersBankCard::create([
            'user_id'=>$request->user_id,
            'bank_name'=>$bank_name,
            'truename'=>$truename,
            'cardnum'=>$cardnum,
            'mobile'=>$mobile,
        ]);

        return $this->success('添加成功');
    }

}
