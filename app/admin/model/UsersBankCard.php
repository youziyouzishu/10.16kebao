<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;
use support\exception\BusinessException;

/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property string $bank_name 银行名称
 * @property string $truename 真实姓名
 * @property string $cardnum 银行卡号
 * @property string $mobile 预留手机号
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersBankCard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersBankCard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersBankCard query()
 * @mixin \Eloquent
 */
class UsersBankCard extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_users_bank_card';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id', 'bank_name', 'truename', 'cardnum', 'mobile'
    ];

    public static function getUserBankCardById($id)
    {
        $row = self::find($id);
        if (!$row){
            throw new BusinessException('银行卡不存在', 1);
        }
        return $row;
    }



}
