<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;

/**
 * 
 *
 * @property int $id 主键
 * @property int $name 等级
 * @property int $num 触发X成员
 * @property int $level_name 触发X等级
 * @property string $amount 触发团队销售额
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Level newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Level newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Level query()
 * @property float $release_rate 签到释放比例
 * @mixin \Eloquent
 */
class Level extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_level';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';



}
