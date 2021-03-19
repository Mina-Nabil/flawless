<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionItem extends Model
{
    protected   $table      = "session_items";
    public      $timestamps = false;

    protected $fillable = [
        "SHIT_PLIT_ID", "SHIT_PRCE", "SHIT_QNTY", "SHIT_TOTL"
    ];

    private     $device;

    public function session(){
        return $this->belongsTo(Session::class, "SHIT_SSHN_ID");
    }

    public function pricelistItem(){
        return $this->belongsTo(PriceListItem::class, "SHIT_PLIT_ID");
    }

    public function availableServices(){
        return $this->deviceObj()->availableServices($this->session->SSHN_PTNT_ID);
    }

    public function deviceObj(){
        if(isset($this->device)) return $this->device;
        $this->device = Device::findOrFail($this->pricelistItem->PLIT_DVIC_ID);
        return $this->device;
    }

}
