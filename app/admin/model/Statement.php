<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;
use plugin\admin\app\model\User;


/**
 * 
 *
 * @property int $id 主键
 * @property string $sale_amount 销售额
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Statement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Statement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Statement query()
 * @property string|null $statistics_date 统计日期
 * @property float $increase_rate 增长率
 * @mixin \Eloquent
 */
class Statement extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_statement';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'sale_amount','statistics_date','increase_rate'
    ];


    
    
}
