<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;
use plugin\admin\app\model\User;


/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property string $charge 手续费
 * @property string $use_confirm_trade_score 出售的确权交易
 * @property int $status 类型:0=待审核,1=已打款,2=拒绝
 * @property int $type 提现类型:0=银行卡提现,1=微信提现,2=支付宝提现
 * @property string $deposit_score 到账金额
 * @property string $reason 拒绝原因
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property string $bank_name 银行名称
 * @property string $truename 真实姓名
 * @property string $cardnum 银行卡号
 * @property string $mobile 预留手机号
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfirmTradeScoreLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfirmTradeScoreLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConfirmTradeScoreLog query()
 * @property-read User|null $user
 * @mixin \Eloquent
 */
class ConfirmTradeScoreLog extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_confirm_trade_score_log';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id', 'charge', 'use_confirm_trade_score', 'status', 'type', 'deposit_score', 'reason', 'bank_name', 'truename', 'cardnum', 'mobile'
    ];

    function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }


}
