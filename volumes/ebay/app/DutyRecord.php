<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\DutyRecord
 *
 * @property integer $id
 * @property string $date 記錄日期
 * @property string $processRecord 檢貨紀錄
 * @property string $printRecord 列印紀錄
 * @property string $trackingRecord 追蹤碼紀錄
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\DutyRecord whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\DutyRecord whereDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\DutyRecord whereProcessRecord($value)
 * @method static \Illuminate\Database\Query\Builder|\App\DutyRecord wherePrintRecord($value)
 * @method static \Illuminate\Database\Query\Builder|\App\DutyRecord whereTrackingRecord($value)
 * @method static \Illuminate\Database\Query\Builder|\App\DutyRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\DutyRecord whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DutyRecord extends Model
{
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'dutyRecord';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date' , 'processRecord' , 'printRecord' , 'trackingRecord'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    

}
