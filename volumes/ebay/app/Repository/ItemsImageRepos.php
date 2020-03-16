<?php
/**
 * Created by PhpStorm.
 * User: Overmind
 * Date: 2016/11/12
 * Time: 下午 04:31
 */
namespace App\Repository;

use App\ItemsImage;
use Illuminate\Support\Collection;

class ItemsImageRepos  {

    /**
     * @var itemsImage
     */
    private $itemsImage;

    public function __construct(ItemsImage $itemsImage) {
        $this->itemsImage = $itemsImage;
    }

    /**
     * @param int $itemId   ebay Item id
     * @return bool|itemsImage 有資料就回傳真 沒有回傳假
     */
    public function findHasItem($itemId) {
        $items = $this->itemsImage->where('itemId' , $itemId)->first();
        if($items != null) {
            return $items;
        } else {
            return false;
        }
        //if($items->count()) {
        //    return true;
        //} else {
        //    return false;
        //}
    }

    /**
     * 新增物品的圖片資料
     * @param array $itemData 新增的資料陣列
     * @return itemsImage 新增的資料collection
     */
    public function insertNewItemImage( array $itemData) {
        $newItem = $this->itemsImage->create($itemData);
        return $newItem;
    }

    /**
     * 依物品id 回傳圖片陸境資料
     * @param $itemId
     * @return Collection|itemsImage
     */
    public function getImageByItemId( $itemId ) {
        return $this->itemsImage->select(['id','itemId','path'])->where('itemId' , $itemId)->take(1);
    }

    /**
     * 依物品id去搜尋圖片位置
     * @param array $itemsId    物品id陣列
     * @return Collection|itemsImage
     */
    public function getImagesByItemsId( array $itemsId) {
        return $this->itemsImage->select(['id','itemId','path'])->whereIn('itemId' ,$itemsId )->get();
    }

}