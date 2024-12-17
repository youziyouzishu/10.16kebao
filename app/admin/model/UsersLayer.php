<?php

namespace app\admin\model;


use plugin\admin\app\model\Base;

/**
 * 
 *
 * @property int $id 主键
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property int|null $user_id
 * @property int|null $parent_id
 * @property int|null $layer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersLayer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersLayer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersLayer query()
 * @mixin \Eloquent
 */
class UsersLayer extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_users_layer';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id', 'parent_id', 'layer'
    ];



}
