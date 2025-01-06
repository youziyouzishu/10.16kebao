<?php

namespace app\admin\model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use plugin\admin\app\model\Base;
use plugin\admin\app\model\User;
use support\exception\BusinessException;


/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property int $admin_id 所属管理员
 * @property string $person 法人姓名
 * @property string $mobile 手机号
 * @property string $name 商家名称
 * @property string $business_front 营业执照正面
 * @property string $bank_account 法人银行账号
 * @property string $main_class 主营类目
 * @property string $address 详细地址
 * @property string $face_image 门面
 * @property int $status 状态1=待支付,2=待审核,3=审核通过,4=审核未通过,5=撤销申请
 * @property string $card_front 证件正面
 * @property string $card_reverse 证件反面
 * @property string $security_amount 保证金
 * @property string $reason 驳回原因
 * @property int $class_id 所属行业
 * @property float $rating 店铺评分
 * @property int $assess_num 评价数量
 * @property int $sale_num 销量
 * @property float $describe_rating 描述评分
 * @property float $serve_rating 服务评分
 * @property float $express_rating 物流评分
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property string $lat 纬度
 * @property string $lng 经度
 * @property int $collect_num 关注数量
 * @property string|null $content 店铺简介
 * @property-read mixed $status_text
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \app\admin\model\ShopGoods> $goods
 * @property-read User|null $user
 * @method static Builder<static>|Shop newModelQuery()
 * @method static Builder<static>|Shop newQuery()
 * @method static Builder<static>|Shop orderByDistance( $latitude,  $longitude)
 * @method static Builder<static>|Shop query()
 * @property string $money 余额
 * @property string $rate 让利百分比
 * @property string $open_time 营业时间
 * @property-read mixed $open_time_text
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \app\admin\model\ShopGoods> $topGoods
 * @property string $province 省
 * @property string $city 市
 * @property string $region 区
 * @property string $return_name 退货人姓名
 * @property string $return_mobile 退货人手机号
 * @property string $return_province 退货省
 * @property string $return_city 退货市
 * @property string $return_region 退货区
 * @property string $return_address 退货地址
 * @property-read \app\admin\model\ShopClass|null $class
 * @mixin \Eloquent
 */
class Shop extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_shop';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id', 'admin_id', 'person', 'mobile', 'name', 'business_front', 'bank_account', 'main_class', 'address', 'face_image', 'status', 'card_front', 'card_reverse', 'security_amount', 'reason', 'class_id', 'rating', 'assess_num', 'sale_num', 'describe_rating', 'serve_rating', 'express_rating', 'lat', 'lng', 'collect_num', 'content','money','rate','open_time','province','city','region'
    ];

    protected $appends = ['status_text','open_time_text'];

    function getStatusTextAttribute($value)
    {
        $value = $value ?: ($this->status ?? '');
        $list = $this->getStatusList();
        return $list[$value] ?? '';
    }

    public function getStatusList()
    {
        return ['1' => '待支付', '2' => '待审核' , '3'=> '审核通过', '4' => '审核未通过','5'=>'撤销申请'];
    }

    function getOpenTimeTextAttribute($value)
    {
        $value = $value ?: ($this->open_time ?? '');
        if (empty($value)){
            return '该商家未设置营业时间';
        }
        if ($this->isOpen($value)){

            return '营业中 '.$value;
        }else{
            return '休息中 '.$value.'开始营业';
        }
    }

    public function isOpen($open_time)
    {

        // 假设 open_time 格式为 "0:0 - 23:57"
        list($startTime,$endTime) = explode(' - ', $open_time);
        // 格式化时间字符串，确保小时和分钟都是两位数
        $startTime = $this->formatTime($startTime);
        $endTime = $this->formatTime($endTime);

        // 解析开始时间和结束时间
        $startTime = Carbon::createFromFormat('H:i', $startTime);
        $endTime = Carbon::createFromFormat('H:i', $endTime);
        // 获取当前时间
        $currentTime = Carbon::now();

        // 如果结束时间小于开始时间，说明跨天了，例如 22:00 - 2:00
        if ($endTime < $startTime) {
            // 如果当前时间大于等于开始时间或小于等于结束时间，则认为在营业时间内
            return $currentTime >= $startTime || $currentTime <= $endTime;
        } else {
            // 否则，直接比较当前时间是否在开始时间和结束时间之间
            return $currentTime >= $startTime && $currentTime <= $endTime;
        }
    }


    private function formatTime($time)
    {
        // 分割小时和分钟
        list($hour, $minute) = explode(':', $time);

        // 使用 str_pad 确保小时和分钟都是两位数
        $hour = str_pad($hour, 2, '0', STR_PAD_LEFT);
        $minute = str_pad($minute, 2, '0', STR_PAD_LEFT);

        // 返回格式化后的时间字符串
        return $hour . ':' . $minute;
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }

    function class()
    {
        return $this->belongsTo(ShopClass::class,'class_id');
    }

    //判断是否商家
    function isShop($user_id)
    {
        return self::where(['user_id'=>$user_id,'status'=>3])->exists();
    }

    public function scopeOrderByDistance(Builder $query,  $latitude,  $longitude): Builder
    {
        return $query->selectRaw('*, (6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance', [$latitude, $longitude, $latitude]);
    }

    public static function getShopById($id)
    {
        $row = self::find($id);
        if (!$row){
            throw new BusinessException('店铺不存在',1);
        }
        return $row;
    }

    function goods()
    {
        return $this->hasMany(ShopGoods::class,'shop_id');
    }

    function topGoods()
    {
        return $this->hasMany(ShopGoods::class,'shop_id')->orderByDesc('weigh')->limit(3);
    }

    public static function isCollect(int $user_id,int $shop_id)
    {
        return UsersCollect::where('user_id', $user_id)->where('pro_id', $shop_id)->where('type', 'shop')->exists();
    }

    public static function getShopByAdminId(int $admin_id)
    {
        $row = self::where('admin_id',$admin_id)->first();
        if (!$row){
            throw new BusinessException('管理员不存在',1);
        }
        return $row;
    }



}
