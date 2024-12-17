<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;

/**
 * 
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property string $class_name 分类
 * @property string $content 内容
 * @property string $image 图片
 * @property string $name 姓名
 * @property string $mobile 电话
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersFeedback newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersFeedback newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersFeedback query()
 * @mixin \Eloquent
 */
class UsersFeedback extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_users_feedback';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id', 'class_name', 'content', 'image', 'name', 'mobile'
    ];



}
