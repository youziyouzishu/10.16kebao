<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;
use plugin\admin\app\model\User;


/**
 * 
 *
 * @property int $id 主键
 * @property string $contribution_charge 贡献值手续费
 * @property string $use_contribution_score 消耗的贡献值
 * @property string $await_confirm_score 待确权积分
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionLog query()
 * @property int $user_id 用户
 * @property-read User|null $user
 * @mixin \Eloquent
 */
class ContributionLog extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_contribution_log';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = ['user_id','contribution_id','contribution_charge','use_contribution_score','await_confirm_score'];

    function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }



}
