<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\record
 *
 * @property integer $id
 * @property string $type 類別
 * @property integer $record 紀錄處理的單號
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\record whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\record whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\record whereRecord($value)
 * @method static \Illuminate\Database\Query\Builder|\App\record whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\record whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\record getRecordByType($type)
 * @mixin \Eloquent
 */
class record extends Model
{
    protected $table = 'record';

    protected $hidden = [];
    protected $fillable = [];

    private  $recordType = [
        1   => 'process',
        2   => 'shipping',
        3   => 'tracking',
    ];


    public function scopeGetRecordByType($query , $type) {
        $typeCollection = collect($this->recordType);
        $key =  $typeCollection->search($type);
        return $record = $query->find($key);
    }

}