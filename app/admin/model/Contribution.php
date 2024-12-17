<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;
use support\exception\BusinessException;


/**
 * 
 *
 * @property int $id 主键
 * @property string $green_score 需要X绿色积分
 * @property string $contribution_score 兑换X贡献值
 * @property string $charge 手续费%
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution query()
 * @mixin \Eloquent
 */
class Contribution extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_contribution';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = ['green_score','contribution_score','charge'];


    public static function getContributionById($id)
    {
        $row = self::find($id);
        if (!$row){
            throw new BusinessException('记录不存在',1);
        }
        return $row;
    }



}
