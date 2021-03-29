<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Device;
use App\Models\PriceList;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{

    function devices()
    {
        $this->data['title']    =       "Devices & Areas Settings";
        $this->data['devices']  =       Device::all();
        $this->data['areas']    =       Area::all();
        $this->data['addDeviceURL'] =   url('add/device');
        $this->data['editDeviceURL'] =  url('edit/device');
        $this->data['delDeviceURL'] =   url('delete/device');
        $this->data['addAreaURL'] =     url('add/area');
        $this->data['editAreaURL'] =    url('edit/area');
        $this->data['delAreaURL'] =     url('delete/area');

        return view("settings.devices", $this->data);
    }

    function pricelists()
    {
        $this->data['title']    =       "Manage Price Lists";
        $this->data['pricelists']  =       PriceList::all();
        $this->data['devices']  =       Device::all();
        $this->data['areas']  =       Area::all();
        $this->data['addPricelistURL'] =   url('add/pricelist');
        $this->data['editPricelistURL'] =  url('edit/pricelist');
        $this->data['delPricelistURL'] =   url('delete/pricelist');
        $this->data['syncPricelistItemsURL'] =   url('sync/pricelist/items');
        $this->data['getPricelistItems'] =   url('get/pricelist/items');

        return view("settings.pricelists", $this->data);
    }

    //price list functions
    function addPricelist(Request $request)
    {
        $request->validate([
            "name" => "required|unique:pricelists,PRLS_NAME",
        ]);

        $pricelist = new PriceList();
        $pricelist->PRLS_NAME = $request->name;
        $pricelist->PRLS_DFLT = $request->isDefault ? 1 : 0;

        $pricelist->save();
        if ($pricelist->PRLS_DFLT)
            PriceList::setDefaultPriceList($pricelist->id);
        return $pricelist->id;
    }
    function editPricelist(Request $request)
    {

        $request->validate([
            "id"   => "required",
        ]);

        $pricelist = PriceList::findOrFail($request->id);

        $request->validate([
            "id"   => "required",
            "name" => ["required", Rule::unique('pricelists', "PRLS_NAME")->ignore($pricelist->PRLS_NAME, "PRLS_NAME"),],
        ]);

        $pricelist->PRLS_NAME = $request->name;
        $pricelist->PRLS_DFLT = $request->isDefault ? 1 : 0;

        $pricelist->save();
        if ($pricelist->PRLS_DFLT)
            PriceList::setDefaultPriceList($pricelist->id);
        return $pricelist->id;
    }

    function deletePricelist(Request $request)
    {
        $request->validate([
            "id"   => "required",
        ]);

        $pricelist = PriceList::findOrFail($request->id);
        $pricelist->deleteAll();
        return "1";
    }

    function syncPricelist(Request $request)
    {
        return;
        $request->validate([
            "id"   => "required",
        ]);

        $pricelist = PriceList::findOrFail($request->id);
        $pricelist->pricelistItems;
        
        foreach ($request->device as $key => $deviceID) {
            $pricelistItem = [
                "PLIT_DVIC_ID"  =>  $deviceID,
                "PLIT_PRCE"     =>  $request->price[$key],
            ];
            if ($request->service[$key] == -1) {
                $pricelistItem["PLIT_TYPE"] = "Pulse";
                $pricelistItem["PLIT_AREA_ID"] = NULL;
            } elseif ($request->service[$key] == 0) {
                $pricelistItem["PLIT_TYPE"] = "Session";
                $pricelistItem["PLIT_AREA_ID"] = NULL;
            } elseif ($request->service[$key] > 0) {
                $pricelistItem["PLIT_TYPE"] = "Area";
                $pricelistItem["PLIT_AREA_ID"] = $request->service[$key];
            }

            foreach($pricelist->pricelistItems as $savedItem){
                if($savedItem->PLIT_DVIC_ID == $pricelistItem["PLIT_DVIC_ID"]){
                    //device mawgood
                }else {
                    $pricelist->pricelistItems()->create($pricelistItem);
                }
            }

            dd([$pricelist->pricelistItems, $pricelistItem]);
        
        }

       return redirect("settings/pricelists");
    }

    function getPricelistItems(Request $request){
        $request->validate([
            "id" => "required"
        ]);
        $pricelist = PriceList::findOrFail($request->id);
        $items = $pricelist->pricelistItems;
        $ret = [];
        foreach ($items as $item) {
            array_push( $ret,  [
                "PLIT_DVIC_ID" => $item->PLIT_DVIC_ID,
                "PLIT_AREA_ID" => ($item->PLIT_TYPE == "Session") ? 0 : (($item->PLIT_TYPE == "Pulse") ? -1 : $item->PLIT_AREA_ID),
                "PLIT_PRCE" => $item->PLIT_PRCE,
            ]);
        }
    
        echo json_encode($ret);
    }

    ///devices functions
    function addDevice(Request $request)
    {
        $request->validate([
            "name" => "required|unique:devices,DVIC_NAME",
        ]);

        $device = new Device();
        $device->DVIC_NAME = $request->name;

        $device->save();
        return $device->id;
    }
    function editDevice(Request $request)
    {

        $request->validate([
            "id"   => "required",
        ]);

        $device = Device::findOrFail($request->id);

        $request->validate([
            "id"   => "required",
            "name" => ["required", Rule::unique('devices', "DVIC_NAME")->ignore($device->DVIC_NAME, "DVIC_NAME"),],
        ]);

        $device->DVIC_NAME = $request->name;

        $device->save();
        return $device->id;
    }

    function deleteDevice(Request $request)
    {
        $request->validate([
            "id"   => "required",
        ]);

        $device = Device::findOrFail($request->id);
        $device->deleteAll();
        return "1";
    }

    ////area functions
    function addArea(Request $request)
    {

        $request->validate([
            "name" => "required|unique:areas,AREA_NAME",
        ]);

        $area = new Area();
        $area->AREA_NAME = $request->name;
        $area->save();
        return $area->id;
    }
    function editArea(Request $request)
    {

        $request->validate([
            "id"   => "required",
        ]);

        $area = Area::findOrFail($request->id);

        $request->validate([
            "id"   => "required",
            "name" => ["required", Rule::unique('areas', "AREA_NAME")->ignore($area->AREA_NAME, "AREA_NAME"),],
        ]);

        $area->AREA_NAME = $request->name;
        $area->save();
        return $area->id;
    }

    function deleteArea(Request $request)
    {
        $request->validate([
            "id"   => "required",
        ]);

        $area = Area::findOrFail($request->id);
        $area->deleteAll();
        return "1";
    }
}
