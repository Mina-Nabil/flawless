<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceListItem extends Model
{
    protected $table = "pricelist_items";
    public $timestamps = false;

    public $fillable = ["PLIT_DVIC_ID", "PLIT_PRCE", "PLIT_TYPE", "PLIT_AREA_ID"] ;

    public static function calculateTotalDuration($servicesIDs){
        return self::whereIn("id", $servicesIDs)->selectRaw("SUM(PLIT_DURT) as total_duration")->first()->total_duration;
    }

    public function getItemNameAttribute(){
        $this->loadMissing("device");
        $ret = $this->device->DVIC_NAME;
        if($this->PLIT_TYPE == "Pulse"){
            $ret .= " Pulses";
        } else if($this->PLIT_TYPE == "Session") {
            $ret .= " Sessions";
        } else {
            $this->loadMissing("area");
            $ret .= " (" . $this->area->AREA_NAME . ")";
        }
        return $ret;
    }

    public function area(){
        return $this->belongsTo(Area::class, 'PLIT_AREA_ID');
    }

    public function device(){
        return $this->belongsTo(Device::class, 'PLIT_DVIC_ID');
    }
}
