<?php

namespace App\Http\Controllers;

use App\Models\DashType;
use App\Models\DashUser;
use Exception;
use Illuminate\Http\Request;

class DashUsersController extends Controller
{
    protected $data;
    //init page data
    private function initDataArr($accountType)
    {
        $this->data['items'] = DashUser::where("DASH_TYPE_ID", $accountType)->with('dash_types')->get();
        $this->data['types'] = DashType::all();
        $this->data['title'] = ($accountType == 1) ? "Application Admins" : "Doctor Accounts";
        $this->data['subTitle'] = ($accountType == 1) ? "Manage, Add and Delete Admins data" : "Manage, Add and Delete Doctors data";
        $this->data['formTitle'] = ($accountType == 1) ? "Add Admins" : "Add Doctors";
        $this->data['cols'] = ['Username', 'Fullname', 'Mob#', 'Type', 'Active', 'Edit'];
        $this->data['atts'] = ['DASH_USNM', 'DASH_FLNM', 'DASH_MOBN', ['foreign' => ['rel' => 'dash_types', 'att' => 'DHTP_NAME']],     [
            'toggle' => [
                "att"   =>  "DASH_ACTV",
                "url"   =>  "dash/users/toggle/",
                "states" => [
                    "1" => "Active",
                    "0" => "Disabled",
                ],
                "actions" => [
                    "1" => "disable the User",
                    "0" => "Activate the User",
                ],
                "classes" => [
                    "1" => "label-success",
                    "0" => "label-danger",
                ],
            ]
        ], ['edit' => ['url' => 'dash/users/edit/', 'att' => 'id']]];
        $this->data['homeURL'] = 'dash/users/' . $accountType;
        $this->data['userType'] = $accountType;
    }

    public function index($accountType)
    {

        $this->initDataArr($accountType);

        $this->data['isPassNeeded'] = true;
        $this->data['formURL'] = "dash/users/insert";
        $this->data['isCancel'] = false;
        return view("auth.dashusers", $this->data);
    }

    public function edit($id)
    {
        $this->data['user'] = DashUser::findOrFail($id);
        $this->initDataArr($this->data['user']->DASH_TYPE_ID);
        $this->data['formTitle'] = "Manage User (" . $this->data['user']->DASH_USNM . ')';
        $this->data['isPassNeeded'] = false;
        $this->data['formURL'] = "dash/users/update";
        $this->data['isCancel'] = true;
        return view("auth.dashusers", $this->data);
    }

    public function insert(Request $request)
    {
        $dashUser = new DashUser;

        $request->validate([
            'name' => 'required',
            'fullname' => "required",
            'type' => 'required',
            'password' => 'required'
        ]);

        $dashUser->DASH_USNM = $request->name;
        $dashUser->DASH_FLNM = $request->fullname;
        $dashUser->DASH_TYPE_ID = $request->type;
        $dashUser->DASH_MOBN = $request->mobn;
        $dashUser->DASH_PASS = bcrypt($request->password);

        if ($request->hasFile('photo')) {
            $dashUser->DASH_IMGE = $request->photo->store('images/users', 'public');
        }

        $dashUser->save();

        return redirect("dash/users/" . $dashUser->DASH_TYPE_ID);
    }

    public function update(Request $request)
    {

        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'fullname' => "required",
            'type' => 'required'
        ]);

        $dashUser = DashUser::findOrFail($request->id);

        $dashUser->DASH_USNM = $request->name;
        $dashUser->DASH_FLNM = $request->fullname;
        $dashUser->DASH_MOBN = $request->mobn;
        $dashUser->DASH_TYPE_ID = $request->type;

        if (isset($request->password)  && strcmp(trim($request->password), '') != 0) {
            $dashUser->DASH_PASS = bcrypt($request->password);
        }

        if ($request->hasFile('photo')) {
            $dashUser->DASH_IMGE = $request->photo->store('images/users', 'public');
            if (file_exists($request->oldPath)) {
                unlink($request->oldPath);
            }
        }

        $dashUser->save();

        return redirect("dash/users/" . $dashUser->DASH_TYPE_ID);
    }

    public function toggle($userID){
        $dashUser = DashUser::findOrFail($userID);
        $dashUser->toggle();
        return redirect("dash/users/" . $dashUser->DASH_TYPE_ID);
    }

    public function delete($userID){
        $dashUser = DashUser::findOrFail($userID);
        try {
            $dashUser->delete();
        } catch (Exception $e) { }
        return redirect("dash/users/" . $dashUser->DASH_TYPE_ID);
    }
}
