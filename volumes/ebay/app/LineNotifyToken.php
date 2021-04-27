<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\LineNotifyToken
 *
 * @property integer $id
 * @property string $token lineNotifyToken 
 * @mixin \Eloquent
 */
class LineNotifyToken extends Model
{
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lineNotifyToken';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token' 
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    

}
