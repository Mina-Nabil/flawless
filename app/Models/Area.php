<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    public $timestamps = false;

    public function priceListItems()
    {
        return $this->hasMany('App\Models\PriceListItem', 'PRLS_AREA_ID');
    }

    //delete 
    function deleteAll()
    {
        $this->priceListItems()->delete();
        $this->delete();
    }
}
