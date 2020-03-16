<?php
/**
 * Created by PhpStorm.
 * User: Overmind
 * Date: 2016/11/2
 * Time: 下午 09:04
 */

namespace App\Repository;

use App\DutyRecord;
use Illuminate\Support\Collection;

class DutyRecordRepos {
    /**
     * @var DutyRecord $dutyRecord
     */
    private $dutyRecord;

    public function __construct(DutyRecord $dutyRecord)
    {
        $this->dutyRecord = $dutyRecord;

    }

    public function getDutyRecordByDate($date) {
        return $this->dutyRecord->where('date' , '=' ,$date)->first();
    }

    /**
     * 新增紀錄
     * @param   array       $post     $post物件
     * @return  DutyRecord  回傳新增好的Model物件
     */
    public function insertNewRecord($post) {
        return $this->dutyRecord->create($post);
    }

    /**
     * 更新紀錄
     * @param  array $post 要更新的資料
     * @return bool       看有沒有更新成功
     */
    public function updateRecord($post) {
        $record = $this->dutyRecord->find($post['id']);
        return $record->update($post);
    }

    /**
     * 依頁數取得資料
     * @param   Int     $page   頁數
     * @return  array           資料陣列
     */
    public function getDutyRecordListByPage($page) {
        $total      = $this->dutyRecord->select('id')->count();
        $offset     = ($page - 1) * 30;
        $recordList = $this->dutyRecord->orderBy('id' , 'desc')
                                       ->offset($offset)
                                       ->limit(30)->get();

        $object = [
            'totalPage'     => ceil($total/30),
            'total'         => $total ,
            'page'          => $page ,
            'recordList'    => $recordList
        ];

        return $object;
    }
}
