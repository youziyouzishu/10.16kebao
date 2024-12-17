<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;

/**
 * 
 *
 * @property integer $id 主键(主键)
 * @property string $name 名称
 * @property string $image 图标
 * @property integer $weigh 权重
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsClass newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsClass newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodsClass query()
 * @mixin \Eloquent
 */
class GoodsClass extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_goods_class';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    
    
    
}
