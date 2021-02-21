<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    protected $table = "pricelists";
    public $timestamps = false;


    function patients(){
        return $this->hasMany('App\Models\Patient', 'PTNT_PRLS_ID');
    }

    function pricelistItems(){
        return $this->hasMany('App\Models\PriceListItem', 'PLIT_PRLS_ID');
    }

    public static function getDefaultList(){
        return  self::where('PRLS_DFLT', 1)->first();
    }


    function deleteAll(){
        $this->patients()->update([
            "PTNT_PRLS_ID" => NULL
        ]);
        $this->pricelistItems()->delete();
        $this->delete();    
    }

    public static function setDefaultPriceList($id){
        self::where("id", "!=", $id)->update([
            "PRLS_DFLT" => 0
        ]);
    }
}
