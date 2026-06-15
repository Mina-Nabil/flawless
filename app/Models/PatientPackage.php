<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientPackage extends Model
{
    // protected $with = ["pricelistItem", "pricelistItem.area", "pricelistItem.device"];
    protected $fillable = ["PTPK_PLIT_ID", "PTPK_QNTY", "PTPK_PRCE", "PTPK_USER_ID", "PTPK_DATE"];
    protected $casts = ["PTPK_DATE" => "datetime"];
    public $timestamps = false;

    //relations
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, "PTPK_PTNT_ID");
    }
    public function pricelistItem(): BelongsTo
    {
        return $this->belongsTo(PriceListItem::class, "PTPK_PLIT_ID");
    }
    public function seller(): BelongsTo
    {
        return $this->belongsTo(DashUser::class, "PTPK_USER_ID");
    }

    //accessors
    public function getLineTotalAttribute()
    {
        return $this->PTPK_PRCE * $this->PTPK_QNTY;
    }

    public function getPackageNameAttribute()
    {
        $item = $this->pricelistItem;
        if (!$item) {
            return "Unknown";
        }
        $name = $item->device->DVIC_NAME ?? "Package";
        if ($item->PLIT_TYPE ?? false) {
            $name .= " " . $item->PLIT_TYPE;
        }
        if ($item->area ?? false) {
            $name .= " (" . $item->area->AREA_NAME . ")";
        }
        return $name;
    }

    /**
     * Load sold packages filtered by patient channel/location and a date range.
     *
     * @param array $channel_ids  channel ids, or [-1] for all channels
     * @param array $location_ids location ids, or [-1] for all locations
     */
    public static function getSoldPackages(array $channel_ids, array $location_ids, $from = null, $to = null)
    {
        $query = self::with(["patient", "patient.channel", "patient.location", "pricelistItem", "pricelistItem.device", "pricelistItem.area", "seller"])
            ->join("patients", "PTPK_PTNT_ID", "=", "patients.id")
            ->select("patient_packages.*");

        if (!in_array(-1, $channel_ids)) {
            $query = $query->whereIn("patients.PTNT_CHNL_ID", $channel_ids);
        }
        if (!in_array(-1, $location_ids)) {
            $query = $query->whereIn("patients.PTNT_LOCT_ID", $location_ids);
        }
        if ($from != null) {
            $query = $query->where("PTPK_DATE", ">=", $from);
        }
        if ($to != null) {
            $query = $query->where("PTPK_DATE", "<=", $to . " 23:59:59");
        }

        return $query->orderByDesc("PTPK_DATE")->get();
    }
}
