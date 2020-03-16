<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\itemsImage
 *
 * @property integer $id
 * @property integer $itemId 物品id
 * @property string $path 圖片的位置
 * @property \Carbon\Carbon $created_at 時搓
 * @property \Carbon\Carbon $updated_at 時搓
 * @method static \Illuminate\Database\Query\Builder|\App\ItemsImage whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ItemsImage whereItemId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ItemsImage wherePath($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ItemsImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ItemsImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ItemsImage extends Model
{
    //
    protected $table = 'itemsImage';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'itemId' , 'path'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
