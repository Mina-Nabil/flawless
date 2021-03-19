<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    public $timestamps = false;

    public function pricelistItems()
    {
        return $this->hasMany('App\Models\PriceListItem', 'PLIT_DVIC_ID');
    }

    public function availableServices($patientID)
    {
        $ret = [];

        $defaultList = PriceList::getDefaultList();
        $patientList = (Patient::findOrFail($patientID))->pricelist;

        foreach ($defaultList->pricelistItems()->where("PLIT_DVIC_ID", $this->id)->get() as $item) {

            if ($item->PLIT_TYPE == "Pulse") {
                if (!array_key_exists($item->id, $ret))
                    array_push($ret, ["serviceName" => "Pulse", "id" => $item->id]);
            } else if ($item->PLIT_TYPE == "Session") {
                if (!array_key_exists($item->id, $ret))
                    array_push($ret, ["serviceName" => "Session", "id" => $item->id]);
            } else {
                if (!array_key_exists($item->id, $ret))
                    array_push($ret, ["serviceName" => $item->area->AREA_NAME, "id" => $item->id]);
            }
        }
        if (isset($patientList))
            foreach ($patientList->pricelistItems()->where("PLIT_DVIC_ID", $this->id)->get() as $item) {

                if ($item->PLIT_TYPE == "Pulse") {
                    if (!array_key_exists($item->id, $ret))
                        array_push($ret, ["serviceName" => "Pulse", "id" => $item->id]);
                } else if ($item->PLIT_TYPE == "Session") {
                    if (!array_key_exists($item->id, $ret))
                        array_push($ret, ["serviceName" => "Session", "id" => $item->id]);
                } else {
                    if (!array_key_exists($item->id, $ret))
                        array_push($ret, ["serviceName" => $item->area->AREA_NAME, "id" => $item->id]);
                }
            }


        return collect($ret);
    }

    //delete 
    function deleteAll()
    {
        $this->pricelistItems()->delete();
        $this->delete();
    }
}
