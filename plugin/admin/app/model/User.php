<?php

namespace plugin\admin\app\model;

use app\admin\model\Agent;
use app\admin\model\Consumer;
use app\admin\model\Level;
use app\admin\model\Shop;
use app\admin\model\UsersIdentity;
use app\admin\model\UsersLayer;
use app\admin\model\UsersScoreLog;
use Illuminate\Database\Eloquent\SoftDeletes;
use support\Db;
use support\exception\BusinessException;

/**
 * 
 *
 * @property integer $id 主键(主键)
 * @property string $username 用户名
 * @property string $city 所在城市
 * @property string $wechat 微信号
 * @property string $alipay 支付宝
 * @property string $nickname 昵称
 * @property string $password 密码
 * @property string $sex 性别
 * @property string $avatar 头像
 * @property string $email 邮箱
 * @property string $mobile 手机
 * @property integer $level 等级
 * @property string $birthday 生日
 * @property string $money 余额
 * @property integer $score 积分
 * @property string $last_ip 登录ip
 * @property string $join_ip 注册ip
 * @property string $token token
 * @property integer $role 角色
 * @property integer $status 禁用
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @property string $invitecode 邀请码
 * @property int $parent_id 上级ID
 * @property-read User|null $parent
 * @property string|null $last_time 登录时间
 * @property string|null $join_time 注册时间
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property string $openid
 * @property string $confirm_score 确权积分
 * @property string $develop_score 拓展积分
 * @property string $green_score 绿色积分
 * @property string $coupon_score 优惠券额度
 * @property string $confirm_trade_score 确权交易积分
 * @property string $await_confirm_score 待确权积分
 * @property-read Agent|null $agent
 * @property-read Shop|null $shop
 * @property-read UsersIdentity|null $identity
 * @property int $type 类型:0=普通用户,1=官方用户
 * @property string $contribution_score 贡献值
 * @property string $shop_score 门店积分
 * @property string $consume 消费金额
 * @property-read Level|null $levelInfo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $children
 * @property \Illuminate\Support\Carbon|null $deleted_at 删除时间
 * @property string $offset 抵用券数量
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 * @property-read Consumer|null $consumer
 * @property string $consumer_amount 消费商余额
 * @mixin \Eloquent
 */
class User extends Base
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_users';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    protected $fillable = [
        'username',
        'nickname',
        'password',
        'sex',
        'avatar',
        'email',
        'mobile',
        'level',
        'birthday',
        'money',
        'score',
        'last_time',
        'last_ip',
        'join_time',
        'join_ip',
        'token',
        'created_at',
        'updated_at',
        'role',
        'status',
        'invitecode',
        'parent_id',
        'openid',
        'confirm_score',
        'develop_score',
        'green_score',
        'coupon_score',
        'confirm_trade_score',
        'await_confirm_score',
        'type',
        'contribution_score',
        'shop_score',
        'consume',
        'wechat',
        'city',
        'alipay'
    ];

    function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    function agent()
    {
        return $this->hasOne(Agent::class, 'user_id')->where('status', 1);
    }

    function consumer()
    {
        return $this->hasOne(Consumer::class, 'user_id')->where('status', 1);
    }

    function shop()
    {
        return $this->hasOne(Shop::class, 'user_id')->where('status',3);
    }

    function levelInfo()
    {
        return $this->belongsTo(Level::class, 'level', 'name');
    }



    /**
     * 变更会员积分
     * @param int $score 积分
     * @param int $user_id 会员ID
     * @param string $memo 备注
     * @param string $type
     * @throws \Throwable
     */
    public static function score($score, $user_id, $memo, $type)
    {
        Db::connection('plugin.admin.mysql')->beginTransaction();
        try {
            $user = self::lockForUpdate()->find($user_id);
            if ($user && $score != 0) {
                $before = $user->$type;
                $after = $user->$type + $score;
                //更新会员信息
                $user->$type = $after;
                $user->save();
                //写入日志
                UsersScoreLog::create(['user_id' => $user_id, 'score' => $score, 'before' => $before, 'after' => $after, 'memo' => $memo, 'type' => $type]);
            }
            Db::connection('plugin.admin.mysql')->commit();
        } catch (\Throwable $e) {
            Db::connection('plugin.admin.mysql')->rollback();
        }
    }


    public static function getUserById($id)
    {

        $row = self::find($id);
        if (!$row) {
            throw new BusinessException('会员不存在', 1);
        }
        return $row;
    }

    #触发升级
    public static function levelUp(int $id)
    {
        $user = self::getUserById($id);
        $team = UsersLayer::where('parent_id', $user->id)->get();#团队
        $teamIds = $team->pluck('user_id');#团队uid
        $teamUsers = self::whereIn('id', $teamIds);#团队信息
        $teamUsersConsume = $teamUsers->sum('consume'); #团队消费额

        $getNextLevel = Level::where('name', '>', $user->level)->get();
        $getNextLevel->each(function (Level $item) use ($teamUsersConsume, $user) {
            $teamLevelUsers = self::where('parent_id', $user->id)->where('level', '>=', $item->level_name)->count();
            if ($teamUsersConsume >= $item->amount && $teamLevelUsers >= $item->num) {
                $user->level = $item->name;
                $user->save();
            }
        });
    }

    function identity()
    {
        return $this->hasOne(UsersIdentity::class, 'user_id')->where('status',1);
    }


}
