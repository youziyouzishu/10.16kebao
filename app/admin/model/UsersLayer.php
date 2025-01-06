<?php

namespace app\admin\model;


use plugin\admin\app\model\Base;
use plugin\admin\app\model\User;

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
 * @property-read User|null $parent
 * @property-read User|null $user
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

    function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    function parent()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }



}
