<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SessionItem extends Model
{
    protected   $table      = "session_items";
    public      $timestamps = false;

    protected $fillable = [
        "SHIT_PLIT_ID", "SHIT_PRCE", "SHIT_QNTY", "SHIT_TOTL", "SHIT_NOTE", "SHIT_DCTR", "SHIT_CLTD_PCKG"
    ];

    private     $device;
    ////static queries
    public static function getServicesDoneByDoctor($devicesIDs, $doctorID, $from, $to)
    {
        $fromDate   = new Carbon($from);
        $toDate     = new Carbon($to);
        $query = self::join('sessions', 'sessions.id', '=', 'SHIT_SSHN_ID')
            ->join('patients', 'patients.id', '=', 'SSHN_PTNT_ID')
            ->join('pricelist_items', 'pricelist_items.id', '=', 'SHIT_PLIT_ID')
            ->join('devices', 'devices.id', '=', 'PLIT_DVIC_ID')
            ->join('dash_users', 'dash_users.id', '=', 'SSHN_DCTR_ID')
            ->leftJoin('areas', 'areas.id', '=', 'PLIT_AREA_ID')
            ->whereBetween('SSHN_DATE', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
            ->where('SSHN_DCTR_ID', $doctorID)
            ->where('SHIT_DCTR', 1)
            ->select('patients.PTNT_NAME', 'sessions.SSHN_STTS', 'devices.DVIC_NAME', 'SHIT_QNTY', 'SHIT_PRCE', 'SHIT_TOTL', "SHIT_SSHN_ID", "AREA_NAME", 'PLIT_TYPE');

        if (!in_array('0', $devicesIDs)) {
            $query = $query->whereIn('PLIT_DVIC_ID', $devicesIDs);
        }

        return $query->get();
    }

    ////scopes
    public function scopeUncollected($query)
    {
        $query->where("SHIT_CLTD_PCKG", 0);
    }

    public function scopeCollected($query)
    {
        $query->where("SHIT_CLTD_PCKG", 1);
    }

    public function getIsCollectedAttribute()
    {
        return $this->SHIT_CLTD_PCKG == 1;
    }

    public function getIsDoctorAttribute()
    {
        return $this->SHIT_DCTR == 1;
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

    static public function getDeviceTotal($branchID = 0, $deviceID, $from, $to)
    {
        $query = self::join("pricelist_items", "SHIT_PLIT_ID", "=", "pricelist_items.id")->where("PLIT_DVIC_ID", $deviceID)
            ->join("sessions", "sessions.id", "=", "SHIT_SSHN_ID")->where("SSHN_STTS", "Done")
            ->whereBetween("SSHN_DATE", [$from, $to])
            ->selectRaw("SUM(SHIT_TOTL) as toto");
        if ($branchID != 0) {
            $query = $query->where('SSHN_BRCH_ID', $branchID);
        }
        return $query->get()->first()->toto ?? 0;
    }
}
