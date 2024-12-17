<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;
use plugin\admin\app\model\User;


/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property string $await_confirm_charge 待确权手续费
 * @property string $use_await_confirm_score 消耗的待确权权益
 * @property int $type 类型:0=确权权益,1=确权交易
 * @property string $deposit_score 到账的积分
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwaitConfirmScoreLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwaitConfirmScoreLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AwaitConfirmScoreLog query()
 * @property-read User|null $user
 * @mixin \Eloquent
 */
class AwaitConfirmScoreLog extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_await_confirm_score_log';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id', 'await_confirm_charge', 'use_await_confirm_score', 'type', 'deposit_score'
    ];

    function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }



}
