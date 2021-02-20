<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Device;
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
