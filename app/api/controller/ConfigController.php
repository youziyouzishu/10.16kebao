<?php

namespace app\api\controller;

use app\admin\model\Banner;
use app\admin\model\Notice;
use app\admin\model\ShopGoodsSku;
use app\api\basic\Base;
use app\api\common\library\Pay;
use Carbon\Carbon;
use DateTime;
use plugin\admin\app\model\Option;
use support\exception\BusinessException;
use support\Log;
use support\Request;
use Tencent\TLSSigAPIv2;

class ConfigController extends Base
{

    protected $noNeedLogin = ['*'];

    function getNoticeList()
    {
        $rows = Notice::all();
        return $this->success('成功',$rows);
    }

    function noticeDetail(Request $request)
    {
        $notice_id = $request->post('notice_id');
        $row = Notice::find($notice_id);
        $row->increment('page_view');
        return $this->success('成功',$row);
    }

    function getBannerList(Request $request)
    {
        $type = $request->post('type');#类型:1=首页,2=本地生活
        $rows = Banner::orderByDesc('weigh')->where('type',$type)->get();
        return $this->success('成功',$rows);
    }

    function getConfig()
    {
        $name = 'admin_config';
        $config = Option::where('name', $name)->value('value');
        $config = json_decode($config);
        return $this->success('成功', $config);
    }

    function getRules()
    {
        $name = 'admin_config';
        $config = Option::where('name', $name)->value('value');
        $config = json_decode($config);
        $rules = $config->rules;
        return $this->success('成功',$rules);
    }

    #获取腾讯云Sig
    function getTLSSig(Request $request)
    {
        $user_id = $request->post('user_id');
        $api = new TLSSigAPIv2(1600062267, '21917a5d7d616bd74b4a10495e74b8b0cbecbbe518b0fceb4a65626b0d7edbc8'); // 替换为实际AppID和密钥
        $sig = $api->genUserSig($user_id);
        return $this->success('获取成功', ['sig' => $sig]);
    }



}
