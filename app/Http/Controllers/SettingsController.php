<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\DashUser;
use App\Models\Device;
use App\Models\PriceList;
use App\Models\Room;
use App\Models\SessionType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{

    /////////main session types
    function sessions()
    {
        $this->data['title']        =   "Session Types";
        $this->data['rooms']        =   SessionType::all();
        $this->data['doctors']        =   DashUser::doctors();

        $this->data['addSessionTypeURL']   =   url('sessiontypes/add');
        $this->data['editSessionTypeURL']  =   url('sessiontypes/edit');
        $this->data['setRoomStateURL'] = url('sessiontypes/setstate');

        return view("settings.sessiontypes", $this->data);
    }

    function session($id, Request $request)
    {
        /** @var Room */
        $room = Room::findOrFail($id);
        $request->validate([
            "branch_id" =>  "required|exists:branches,id",
            "name"      =>  "required"
        ]);
        $room->updateInfo($request->branch_id, $request->name, $request->desc);
        return redirect()->action([self::class, "rooms"]);
    }
   
    /////////devices
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
        $pricelist->PRLS_DFLT = $request->isDefault == true ? 1 : 0;

        $pricelist->save();
        if ($request->isDefault == true)
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
        $pricelist->PRLS_DFLT = $request->isDefault == true ? 1 : 0;

        $pricelist->save();
        if ($pricelist->PRLS_DFLT) {
            PriceList::setDefaultPriceList($pricelist->id);
        }
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
        try {


            $request->validate([
                "id"   => "required",
            ]);

            $pricelist = PriceList::findOrFail($request->id);
            $pricelist->pricelistItems;

            $itemsArr = [];

            foreach ($request->device as $key => $deviceID) {
                $create = true;
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

                array_push($itemsArr, $pricelistItem);


                foreach ($pricelist->pricelistItems as $savedItem) {
                    if ($savedItem->PLIT_DVIC_ID == $pricelistItem["PLIT_DVIC_ID"]) {
                        //device mawgood
                        if ($savedItem->PLIT_TYPE == $pricelistItem["PLIT_TYPE"]) {
                            //same type
                            if ($savedItem->PLIT_TYPE == "Area") {
                                if ($savedItem->PLIT_AREA_ID == $pricelistItem["PLIT_AREA_ID"]) {
                                    $savedItem->PLIT_PRCE =  $request->price[$key];
                                    $savedItem->save();
                                    $create = false;
                                }
                            } else {
                                $savedItem->PLIT_PRCE =  $request->price[$key];
                                $savedItem->save();
                                $create = false;
                            }
                        }
                    }
                }
                if ($create)
                    $pricelist->pricelistItems()->create($pricelistItem);
            }
            $pricelist->load('pricelistItems');
            ///delete check
            $deleteCount = $pricelist->pricelistItems->count() - count($itemsArr);

            if ($deleteCount > 0) {
                foreach ($pricelist->pricelistItems as $itemToDelete) {
                    $delete = true;
                    foreach ($itemsArr as $itemToCheck) {
                        if (
                            ($itemToDelete->PLIT_DVIC_ID == $itemToCheck["PLIT_DVIC_ID"]) &&
                            ($itemToDelete->PLIT_TYPE == $itemToCheck["PLIT_TYPE"] &&
                                ($itemToDelete->PLIT_TYPE != "Area" || $itemToDelete->PLIT_AREA_ID == $itemToCheck["PLIT_AREA_ID"]))
                        ) {
                            $delete = false;
                            break;
                        }
                    }
                    if ($delete) {
                        $itemToDelete->delete();
                        $deleteCount--;
                    }
                    if ($deleteCount == 0) break;
                }
            }
        } catch (Exception $e) {
        }

        return redirect("settings/pricelists");
    }

    function getPricelistItems(Request $request)
    {
        $request->validate([
            "id" => "required"
        ]);
        $pricelist = PriceList::findOrFail($request->id);
        $items = $pricelist->pricelistItems;
        $ret = [];
        foreach ($items as $item) {
            array_push($ret,  [
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
