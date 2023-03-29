<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockItem extends Model
{

    public $timestamps = false;

    ////transactions
    public static function newTransaction($transArr, string $code = null)
    {
        $transactionCode = $code ?? date_format(now(), "ymdHis");
        $date = date_format(now(), "Y-m-d H:i:s");
        $userID = Auth::user()->id;

        foreach ($transArr as $trans) {
            $lastTrans = DB::table('stock_transactions')
                ->select('STTR_BLNC')
                ->where('STTR_STCK_ID', $trans['stock_id'])
                ->orderByDesc('id')->limit(1)->first();

            $balance = $lastTrans ? ($lastTrans->STTR_BLNC + $trans['amount']) : $trans['amount'];
            DB::table('stock_transactions')->insert([
                "STTR_CODE"     =>  $transactionCode,
                "STTR_DATE"     =>  $date,
                "STTR_DASH_ID"  =>  $userID,
                "STTR_STCK_ID"  =>  $trans['stock_id'],
                "STTR_AMNT"     =>  $trans['amount'],
                "STTR_BLNC"     =>  $balance
            ]);
        }
    }

    ////model functions
    public function updateItem($name, $unit, $type_id, $is_session)
    {
        $this->STCK_NAME = $name;
        $this->STCK_UNIT = $unit;
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
    public static function loadTransactions()
    {
        return DB::table('stock_transactions')
            ->select('STTR_CODE')
            ->selectRaw('MIN(DASH_USNM) as DASH_USNM')
            ->selectRaw('GROUP_CONCAT(STCK_NAME) as STCK_NAME')
            ->selectRaw('SUM(STTR_AMNT) as STTR_AMNT')
            ->join('stock_items', 'STTR_STCK_ID', '=', 'stock_items.id')
            ->join('dash_users', 'STTR_DASH_ID', '=', 'dash_users.id')
            ->groupBy('STTR_CODE')
            ->orderByDesc('STTR_CODE')
            ->limit(100)
            ->get();
    }

    public static function loadTransaction($code)
    {
        return DB::table('stock_transactions')
            ->select('stock_transactions.*', 'DASH_USNM')
            ->selectRaw('CONCAT(STTP_NAME, " - ", STCK_NAME) as STCK_NAME')
            ->join('stock_items', 'STTR_STCK_ID', '=', 'stock_items.id')
            ->join('stock_types', 'STCK_STTP_ID', '=', 'stock_types.id')
            ->join('dash_users', 'STTR_DASH_ID', '=', 'dash_users.id')
            ->where('STTR_CODE', $code)
            ->get();
    }

    public static function getItems($include_deactive = false)
    {
        $items = self::query();
        if (!$include_deactive) {
            $items->where('STCK_ACTV', 1);
        }
        return $items->get();
    }


    public static function insertNew($name, $unit, $type_id, $is_session)
    {
        $newStockItem = new self;
        $newStockItem->STCK_NAME = $name;
        $newStockItem->STCK_UNIT = $unit;
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

    public function scopeActive($query)
    {
        return $query->where('STCK_ACTV', 1);
    }

    public function scopeSession($query)
    {
        return $query->where('STCK_SSHN', 1);
    }

    ////relations
    public function type()
    {
        return $this->belongsTo(StockType::class, 'STCK_STTP_ID');
    }
}
