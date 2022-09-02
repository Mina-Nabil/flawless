<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomsController extends Controller
{
    function rooms()
    {
        $this->data = self::getRoomsDataArray();

        return view("settings.rooms", $this->data);
    }

    function room($id)
    {
        /** @var Room */
        $this->data = self::getRoomsDataArray($id);

        return view("settings.rooms", $this->data);
    }

    function addRoom(Request $request)
    {
        /** @var Room */
        $request->validate([
            "branch_id" =>  "required|exists:branches,id",
            "name"      =>  "required"
        ]);
        $room = Room::newRoom($request->branch_id, $request->name, $request->desc);
        return redirect()->action([self::class, "rooms"]);
    }

    function updateRoom($id, Request $request)
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

    function roomState($id)
    {
        /** @var Room */
        $room = Room::findOrFail($id);
        $room->toggleState();
        return redirect()->action([self::class, "rooms"]);
    }

    private function getRoomsDataArray($roomID = null)
    {
        $this->data['items'] = Room::all();
        if ($roomID) {
            $this->data['room'] = Room::findOrFail($roomID);
        }
        $this->data['title']        =   "Branch Rooms";
        $this->data['subTitle'] = "Manage Flawless branches rooms";
        $this->data['formTitle'] = ($roomID) ? "Edit " . $this->data['room']->branch->BRCH_NAME . " " . $this->data['room']->ROOM_NAME : "Add Room";

        $this->data['cols'] = ['Branch','Name', 'Desc.', 'Active', 'Edit'];
        $this->data['atts'] = [
            ['foreign' => ['rel' => 'branch', 'att' => 'BRCH_NAME']],
            'ROOM_NAME',
            ['comment' => ['att' => 'ROOM_DESC']],
            [
                'toggle' => [
                    "att"   =>  "ROOM_ACTV",
                    "url"   =>  "rooms/toggle/",
                    "states" => [
                        "1" => "Active",
                        "0" => "Disabled",
                    ],
                    "actions" => [
                        "1" => "disable the Room",
                        "0" => "Activate the Room",
                    ],
                    "classes" => [
                        "1" => "label-success",
                        "0" => "label-danger",
                    ],
                ]
            ],
            ['edit' => ['url' => 'rooms/', 'att' => 'id']]
        ];

        $this->data['formURL']   =  ($roomID) ? url('rooms/update') . '/' . $this->data['room']->id : url('rooms');
        $this->data['homeURL'] = 'rooms';
        $this->data['isCancel'] = $roomID;
        return $this->data;
    }
}
