<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;
use plugin\admin\app\model\User;


/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property int $status 状态:0=待审核,1=通过,2=驳回,3=撤销申请
 * @property string $reason 驳回原因
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property-read mixed $status_text
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent query()
 * @mixin \Eloquent
 */
class Agent extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_agent';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id'
    ];


    function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    protected $appends = ['status_text'];


    function getStatusTextAttribute($value)
    {
        $value = $value ?: ($this->status ?? '');
        $list = $this->getStatusList();
        return $list[$value] ?? '';
    }

    public function getStatusList()
    {
        return [
            0 => '待审核',
            1 => '通过',
            2 => '驳回',
            3 => '撤销申请'
        ];
    }

    
    
}
