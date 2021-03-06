<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceListItem extends Model
{
    protected $table = "pricelist_items";
    public $timestamps = false;

    public $fillable = ["PLIT_DVIC_ID", "PLIT_PRCE", "PLIT_TYPE", "PLIT_AREA_ID"] ;

    public function area(){
        return $this->belongsTo("App\Models\Area", 'PLIT_AREA_ID');
    }

    public function device(){
        return $this->belongsTo("App\Models\Device", 'PLIT_DVIC_ID');
    }
}
