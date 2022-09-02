<?php

namespace App\Http\Controllers;

use App\Models\SessionType;
use Illuminate\Http\Request;

class SessionTypesController extends Controller
{
    function sessiontypes()
    {
        $this->data = self::getSessionTypesDataArray();

        return view("settings.sessiontypes", $this->data);
    }

    function sessiontype($id)
    {
        /** @var SessionType */
        $this->data = self::getSessionTypesDataArray($id);

        return view("settings.sessiontypes", $this->data);
    }

    function addSessionType(Request $request)
    {
        /** @var SessionType */
        $request->validate([
            "name"      =>  "required",
            "doctors"   =>  "array",
            "duration"  =>  "required|numeric|min:0|max:120"
        ]);
        $sessiontype = SessionType::newSessionType($request->name, $request->duration, $request->doctors,  $request->desc);
        return redirect()->action([self::class, "sessiontypes"]);
    }

    function updateSessionType($id, Request $request)
    {
        /** @var SessionType */
        $sessiontype = SessionType::findOrFail($id);
        $request->validate([
            "name"      =>  "required",
            "doctors"   =>  "array",
            "duration"  =>  "required|numeric|min:0|max:120"
        ]);
        $sessiontype->updateInfo($request->name, $request->duration, $request->doctors,  $request->desc);
        return redirect()->action([self::class, "sessiontypes"]);
    }

    function sessiontypeState($id)
    {
        /** @var SessionType */
        $sessiontype = SessionType::findOrFail($id);
        $sessiontype->toggleState();
        return redirect()->action([self::class, "sessiontypes"]);
    }

    private function getSessionTypesDataArray($sessiontypeID = null)
    {
        $this->data['items'] = SessionType::all();
        if ($sessiontypeID) {
            $this->data['sessiontype'] = SessionType::findOrFail($sessiontypeID);
        }
        $this->data['title']        =   "Available Sessions Types";
        $this->data['subTitle'] = "Manage Flawless offered services";
        $this->data['formTitle'] = ($sessiontypeID) ? "Edit " . $this->data['sessiontype']->NAME : "Add SessionType";

        $this->data['cols'] = ['Name', 'Duration (mins)', 'Desc.', 'Active', 'Edit'];
        $this->data['atts'] = [
            'SHTP_NAME',
            'SHTP_DUR',
            ['comment' => ['att' => 'SHTP_DESC']],
            [
                'toggle' => [
                    "att"   =>  "SHTP_ACTV",
                    "url"   =>  "sessiontypes/toggle/",
                    "states" => [
                        "1" => "Active",
                        "0" => "Disabled",
                    ],
                    "actions" => [
                        "1" => "disable this Session Type",
                        "0" => "Activate this Session Type",
                    ],
                    "classes" => [
                        "1" => "label-success",
                        "0" => "label-danger",
                    ],
                ]
            ],
            ['edit' => ['url' => 'sessiontypes/', 'att' => 'id']]
        ];

        $this->data['formURL']   =  ($sessiontypeID) ? url('sessiontypes/update') . '/' . $this->data['sessiontype']->id : url('sessiontypes');
        $this->data['homeURL'] = 'sessiontypes';
        $this->data['isCancel'] = $sessiontypeID;
        return $this->data;
    }
}
