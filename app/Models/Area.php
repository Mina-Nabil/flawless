<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    public $timestamps = false;

    public function pricelistItems()
    {
        return $this->hasMany('App\Models\PriceListItem', 'PLIT_AREA_ID');
    }

    //delete 
    function deleteAll()
    {
        $this->pricelistItems()->delete();
        $this->delete();
    }
}
