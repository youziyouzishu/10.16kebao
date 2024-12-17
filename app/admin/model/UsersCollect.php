<?php

namespace app\admin\model;

use plugin\admin\app\model\Base;

/**
 * 
 *
 * @property int $id 主键
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property int $user_id 用户
 * @property string $type 类型
 * @property int $pro_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersCollect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersCollect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsersCollect query()
 * @property-read \app\admin\model\ShopGoods|null $goods
 * @property-read \app\admin\model\Shop|null $shop
 * @mixin \Eloquent
 */
class UsersCollect extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_users_collect';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'type',
        'pro_id',
    ];


    public static function doCollect($id,$uid,$type){
        $row = self::where(['user_id'=>$uid,'type'=>$type,'pro_id'=>$id])->first();
        if ($row){
            $row->delete();
            return false;
        }else{
            UsersCollect::create([
                'user_id'=>$uid,
                'type'=>$type,
                'pro_id'=>$id
            ]);
            return true;
        }
    }

    function goods()
    {
        return $this->belongsTo(ShopGoods::class,'pro_id','id');
    }

    function shop()
    {
        return $this->belongsTo(Shop::class,'pro_id','id');
    }
}