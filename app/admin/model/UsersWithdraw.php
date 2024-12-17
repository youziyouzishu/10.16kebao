<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;

/**
 * 
 *
 * @property integer $id 主键(主键)
 * @property integer $user_id 用户
 * @property string $withdraw_amount 提现金额
 * @property string $bank_name 银行卡名称
 * @property string $bank_no 银行卡号
 * @property integer $status 状态:0=待审核,1=已打款,2=驳回
 * @property string $reason 驳回原因
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersWithdraw newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersWithdraw newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersWithdraw query()
 * @mixin \Eloquent
 */
class UsersWithdraw extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_users_withdraw';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    
    
    
}
