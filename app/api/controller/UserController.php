<?php

namespace app\api\controller;

use app\admin\model\Sms;
use app\admin\model\UsersCollect;
use app\admin\model\UsersFeedback;
use app\admin\model\UsersIdentity;
use app\admin\model\UsersLayer;
use app\admin\model\UsersTrack;
use app\api\basic\Base;
use Carbon\Carbon;
use EasyWeChat\MiniApp\Application;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\Eloquent\Builder;
use plugin\admin\app\common\Util;
use plugin\admin\app\model\Dict;
use plugin\admin\app\model\Option;
use plugin\admin\app\model\User;
use Respect\Validation\Validator;
use support\Request;
use support\Response;
use Tinywan\Jwt\JwtToken;

class UserController extends Base
{

    protected $noNeedLogin = ['login'];

    function login(Request $request): Response
    {
        $mobile = $request->post('mobile','');
        $captcha = $request->post('captcha');
        $code = $request->post('code');
        $login_type = $request->post('login_type');# 1=微信登陆 2=手机号登录
        $openid = '';
        if ($login_type == 1) {
            $config = config('wechat');
            $app = new Application($config);
            $res = $app->getUtils()->codeToSession($code);
            $openid = $res['openid'];
            $user = User::where(['openid' => $openid])->first();
        } elseif ($login_type == 2) {
            $smsResult = Sms::check($mobile, $captcha, 'login');
            if (!$smsResult) {
                return $this->fail('验证码错误');
            }
            $user = User::where(['mobile' => $mobile])->first();
        } else {
            return $this->fail('登陆方式错误');
        }

        if (!$user) {
            $user = User::create([
                'avatar' => '/avatar.png',
                'nickname' => '用户' . Util::alnum(),
                'mobile' => $mobile,
                'join_time' => date('Y-m-d H:i:s'),
                'join_ip' => $request->getRealIp(),
                'openid' => $openid
            ]);
        }
        $user->last_time = date('Y-m-d H:i:s');
        $user->last_ip = $request->getRealIp();
        $user->save();
        $token = JwtToken::generateToken([
            'id' => $user->id,
            'client' => JwtToken::TOKEN_CLIENT_MOBILE
        ]);
        return $this->success('登陆成功', ['token'=>$token,'user'=>$user]);
    }

    function register(Request $request): Response
    {
        $mobile = $request->post('mobile');
        $captcha = $request->post('captcha');
        $password = $request->post('password');
        $password_confirm = $request->post('password_confirm');
        $invitecode = $request->post('invitecode');
        if (empty($invitecode)) {
            return $this->fail('邀请码不能为空');
        }
        $parent = User::where('invitecode', $invitecode)->first();
        if ($password !== $password_confirm) {
            return $this->fail('两次密码不一致');
        }
        $captchaResult = Sms::check($mobile, $captcha, 'register');
        if (!$captchaResult) {
            return $this->fail('验证码错误');
        }

        if (!$parent) {
            return $this->fail('邀请码不存在');
        }


        $user = User::create([
            'nickname' => '用户' . Util::alnum(),
            'avatar' => '/app/admin/upload/files/20241014/670c7690a977.jpg',
            'join_time' => Carbon::now()->toDateTimeString(),
            'join_ip' => $request->getRealIp(),
            'last_time' => Carbon::now()->toDateTimeString(),
            'last_ip' => $request->getRealIp(),
            'username' => $mobile,
            'mobile' => $mobile,
            'password' => Util::passwordHash($password),
            'parent_id' => $parent->id,
            'invitecode' => Util::generateInvitecode(),
        ]);


        // 增加直推关系
        UsersLayer::create([
            'user_id' => $user->id,
            'parent_id' => $parent->id,
            'layer' => 1
        ]);

        // 处理多层关系
        $positions = UsersLayer::where('user_id', $parent->id)->get();
        if ($positions->isNotEmpty()) {
            foreach ($positions as $position) {
                UsersLayer::create([
                    'user_id' => $user->id,
                    'parent_id' => $position->parent_id,
                    'layer' => $position->layer + 1
                ]);
            }
        }

        $token = JwtToken::generateToken([
            'id' => $user->id,
            'client' => JwtToken::TOKEN_CLIENT_MOBILE
        ]);
        return $this->success('注册成功', $token);
    }

    #获取个人信息
    function getUserInfo(Request $request)
    {
        $user_id = $request->post('user_id', $request->user_id);
        $row = User::getUserById($user_id);
        $row->load(['agent', 'parent', 'shop','consumer']);
        return $this->success('获取成功', $row);
    }

    function bindMobile(Request $request)
    {
        $code = $request->post('code');
        //小程序
        $app = new Application(config('wechat'));
        $api = $app->getClient();
        $ret = $api->postJson('/wxa/business/getuserphonenumber', [
            'code' => $code
        ]);
        $ret = json_decode($ret);
        if ($ret->errcode != 0) {
            return $this->fail('获取手机号失败');
        }
        $mobile = $ret->phone_info->phoneNumber;
        $row = User::find($request->user_id);
        $row->mobile = $mobile;
        $row->username = $mobile;
        $row->save();
        return $this->success('成功');
    }

    function bindWechat(Request $request)
    {
        $code = $request->post('code');
        $config = config('wechat');
        $app = new Application($config);
        $oauth = $app->getOauth();
        try {
            $response = $oauth->userFromCode($code);
        } catch (\Throwable $e) {
            return $this->fail($e->getMessage());
        }
        $user = User::where(['openid' => $response->getId()])->first();
        if ($user) {
            return $this->fail('此微信已被绑定');
        }
        $user = User::find($request->user_id);
        $user->openid = $response->getId();
        $user->save();
        return $this->success('绑定成功');
    }

    function editUserInfo(Request $request)
    {
        $avatar = $request->post('avatar');
        $nickname = $request->post('nickname');
        $wechat = $request->post('wechat');
        $birthday = $request->post('birthday');
        $city = $request->post('city');

        $data = $request->post();

        $row = User::getUserById($request->user_id);
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $row->setAttribute($key, $value);
            }
        }
//        $row->fill([
//            'avatar' => $avatar,
//            'nickname' => $nickname,
//            'wechat' => $wechat,
//            'birthday' => $birthday,
//            'city' => $city
//        ]);
        $row->save();
        return $this->success('修改成功');
    }

    function changeMobile(Request $request)
    {
        $mobile = $request->post('mobile');
        $captcha = $request->post('captcha');
        if (!$mobile || !Validator::mobile()->validate($mobile)) {
            return $this->fail('手机号不正确');
        }
        $smsResult = Sms::check($mobile, $captcha, 'changemobile');
        if (!$smsResult) {
            return $this->fail('验证码不正确');
        }
        $user = User::find($request->user_id);
        $user->mobile = $mobile;
        $user->username = $mobile;
        $user->save();
        return $this->success();
    }


    #获取团队信息
    function getTeamInfo(Request $request)
    {
        $user = User::getUserById($request->user_id);
        $user->load(['agent', 'parent' => function ($query) {
            $query->with(['agent']);
        }]);
        $team = $user->children()->with(['agent'])->get();
        $team_count = $team->count();
        $team_consume = $team->sum('consume');//直推销售额
        $total_team_ids = UsersLayer::where('parent_id', $request->user_id)->pluck('user_id');
        $total_team_consume = User::whereIn('id', $total_team_ids)->sum('consume');
        return $this->success('获取成功', [
            'user' => $user,
            'team' => $team,
            'team_count' => $team_count,
            'team_consume' => $team_consume,
            'total_team_consume' => $total_team_consume
        ]);
    }

    #海报
    function getPoster(Request $request)
    {
        $user = User::getUserById($request->user_id);
        // 使用构建器创建 QR Code
        $writer = new PngWriter();
        $qrCode = new QrCode(
            data: 'https://0907shangcheng.62.hzgqapp.com/register/register.html#/?invitecode=' . $user->invitecode,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 100,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );
        $result = $writer->write($qrCode)->getDataUri();
        $name = 'admin_config';
        $config = Option::where('name', $name)->value('value');
        $config = json_decode($config);
        $poster = $config->poster;
        return $this->success('获取成功', ['base64' => $result, 'invitecode' => $user->invitecode, 'poster_image' => $poster->poster_image]);
    }

    #实名认证
    function identity(Request $request)
    {
        $truename = $request->post('truename');
        $mobile = $request->post('mobile');
        $cardnum = $request->post('cardnum');
        $expire_day = $request->post('expire_day');
        $front = $request->post('front');
        $back = $request->post('back');
        $captcha = $request->post('captcha');

        $smsResult = Sms::check($mobile, $captcha, 'identity');
        if (!$smsResult) {
            return $this->fail('验证码错误');
        }
        $row = UsersIdentity::where(['user_id' => $request->user_id])->whereIn('status', [0, 1])->exists();
        if ($row) {
            return $this->fail('不能重复提交');
        }
        UsersIdentity::create([
            'user_id' => $request->user_id,
            'truename' => $truename,
            'mobile' => $mobile,
            'cardnum' => $cardnum,
            'expire_day' => $expire_day,
            'front' => $front,
            'back' => $back,
        ]);
        return $this->success('提交成功');
    }

    #收藏记录
    function collect(Request $request)
    {
        $type = $request->post('type'); #类型:goods=商品,shop=商铺
        $rows = UsersCollect::where(['user_id' => $request->user_id, 'type' => $type])
            ->when(!empty($type), function (Builder $query) use ($type) {
                if ($type == 'goods') {
                    $query->with(['goods.shop']);
                } else {
                    $query->with(['shop']);
                }
            })
            ->paginate()
            ->items();
        return $this->success('获取成功', $rows);
    }

    function getFeedbackClass(Request $request)
    {
        $rows = Dict::get('feedbackclass');
        return $this->success('获取成功', $rows);
    }

    function feedback(Request $request)
    {
        $class_id = $request->post('class_id');
        $content = $request->post('content');
        $image = $request->post('image');
        $name = $request->post('name');
        $mobile = $request->post('mobile');
        $class_name = Util::getDictNameByValue('feedbackclass', $class_id);
        UsersFeedback::create([
            'user_id' => $request->user_id,
            'class_name' => $class_name,
            'content' => $content,
            'image' => $image,
            'name' => $name,
            'mobile' => $mobile
        ]);
        return $this->success('提交成功');
    }

    #足迹
    function getUserTrack(Request $request)
    {
        $start_time = $request->post('start_time');
        $end_time = $request->post('end_time');
        // 将时间戳转换为日期格式

        $startDate = Carbon::createFromTimestamp($start_time, 'Asia/Shanghai')->toDateTimeString();
        $endDate = Carbon::createFromTimestamp($end_time, 'Asia/Shanghai')->toDateTimeString();
        $rows = UsersTrack::with(['goods'])->where('user_id', $request->user_id)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->get();
        return $this->success('获取成功', $rows);
    }

    #注销
    function userOff(Request $request)
    {
        $user = User::getUserById($request->user_id);
        $user->delete();
        return $this->success('注销成功');
    }


}
