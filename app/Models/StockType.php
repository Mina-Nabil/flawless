<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

class StockType extends Model
{
    protected $table = 'stock_types';
    public $timestamps = false;

    ///model functions
    public function updateType($name)
    {
        $this->STTP_NAME = $name;
        try {
            return $this->save();
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    ///static functions 
    public static function insertNew($name)
    {
        $newStockType = new self;
        $newStockType->STTP_NAME = $name;
        try {
            $newStockType->save();
            return $newStockType;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }
}
