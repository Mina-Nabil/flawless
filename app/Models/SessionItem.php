<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionItem extends Model
{
    protected   $table      = "session_items";
    public      $timestamps = false;

    protected $fillable = [
        "SHIT_PLIT_ID", "SHIT_PRCE", "SHIT_QNTY", "SHIT_TOTL", "SHIT_NOTE", "SHIT_DCTR", "SHIT_CLTD_PCKG"
    ];

    private     $device;


    ////scopes
    public function scopeUncollected($query){
        $query->where("SHIT_CLTD_PCKG", 0);
    }

    public function getIsDoctorAttribute(){
        return $this->SHIT_DCTR == 1 ;
    }

    public function session()
    {
        return $this->belongsTo(Session::class, "SHIT_SSHN_ID");
    }

    public function pricelistItem()
    {
        return $this->belongsTo(PriceListItem::class, "SHIT_PLIT_ID");
    }

    public function availableServices()
    {
        return $this->deviceObj()->availableServices($this->session->SSHN_PTNT_ID);
    }

    public function deviceObj()
    {
        if (isset($this->device)) return $this->device;
        $this->device = Device::findOrFail($this->pricelistItem->PLIT_DVIC_ID);
        return $this->device;
    }

    static public function getDeviceTotal($deviceID, $from, $to)
    {
        return self::join("pricelist_items", "SHIT_PLIT_ID", "=", "pricelist_items.id")->where("PLIT_DVIC_ID", $deviceID)
            ->join("sessions", "sessions.id", "=", "SHIT_SSHN_ID")->where("SSHN_STTS", "Done")
            ->whereBetween("SSHN_DATE", [$from, $to])
            ->selectRaw("SUM(SHIT_TOTL) as toto")->get()->first()->toto ?? 0;
    }
}
