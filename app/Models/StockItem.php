<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    ///model functions
    public function updateItem($name, $type_id, $is_session)
    {
        $this->STCK_NAME = $name;
        $this->STCK_STTP_ID = $type_id;
        $this->STCK_SSHN = $is_session;
        try {
            return $this->save();
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function toggleState()
    {
        $this->STCK_ACTV = !$this->STCK_ACTV;
        try {
            return $this->save();
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    ///static functions 
    public static function getItems($include_deactive = false)
    {
        $items = self::query();
        if (!$include_deactive) {
            $items->where('STCK_ACTV', 1);
        }
        return $items->get();
    }


    public static function insertNew($name, $type_id, $is_session)
    {
        $newStockItem = new self;
        $newStockItem->STCK_NAME = $name;
        $newStockItem->STCK_STTP_ID = $type_id;
        $newStockItem->STCK_SSHN = $is_session;
        try {
            $newStockItem->save();
            return $newStockItem;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    ////relations
    public function type()
    {
        return $this->belongsTo(StockType::class, 'STCK_STTP_ID');
    }
}
