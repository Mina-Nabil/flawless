<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientPackage extends Model
{
    // protected $with = ["pricelistItem", "pricelistItem.area", "pricelistItem.device"];
    protected $fillable = ["PTPK_PLIT_ID", "PTPK_QNTY", "PTPK_PRCE"];
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
}
