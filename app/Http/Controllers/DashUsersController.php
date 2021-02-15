<?php

namespace App\Http\Controllers;

use App\Models\DashType;
use App\Models\DashUser;
use Illuminate\Http\Request;

class DashUsersController extends Controller
{
    protected $data;
    //init page data
    private function initDataArr()
    {
        $this->data['items'] = DashUser::with('dash_types')->get();
        $this->data['types'] = DashType::all();
        $this->data['title'] = "Dashboard Users";
        $this->data['subTitle'] = "Manage All Dashboard Users";
        $this->data['cols'] = ['Username', 'Fullname', 'Type', 'Edit'];
        $this->data['atts'] = ['DASH_USNM', 'DASH_FLNM', ['foreign' => ['rel'=>'dash_types', 'att'=>'DHTP_NAME']], ['edit' => ['url' => 'dash/users/edit/', 'att' => 'id']]];
        $this->data['homeURL'] = 'dash/users/all';
    }

    public function index()
    {

        $this->initDataArr();
        $this->data['formTitle'] = "Add Admins";
        $this->data['isPassNeeded'] = true;
        $this->data['formURL'] = "dash/users/insert";
        $this->data['isCancel'] = false;
        return view("auth.dashusers", $this->data);
    }

    public function edit($id)
    {
        $this->initDataArr();
        $this->data['user'] = DashUser::findOrFail($id);
        $this->data['formTitle'] = "Manage Admin(" . $this->data['user']->DASH_USNM . ')';
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
        $dashUser->DASH_PASS = bcrypt($request->password);

        if ($request->hasFile('photo')) {
            $dashUser->DASH_IMGE = $request->photo->store('images/users', 'public');
        }

        $dashUser->save();

        return redirect("dash/users/all");
    }

    public function update(Request $request){

        $request->validate([
            'id' => 'required',
            'name' => 'required',
            'fullname' => "required",
            'type' => 'required'
            ]);
            
        $dashUser = DashUser::findOrFail($request->id);
       
        $dashUser->DASH_USNM = $request->name;
        $dashUser->DASH_FLNM = $request->fullname;
        $dashUser->DASH_TYPE_ID = $request->type;

        if(isset($request->password)  && strcmp(trim($request->password), '') != 0){
            $dashUser->DASH_PASS = bcrypt($request->password);
        }

        if ($request->hasFile('photo')) {
            $dashUser->DASH_IMGE = $request->photo->store('images/users', 'public');
            if (file_exists($request->oldPath)){
                unlink($request->oldPath);
            }
        }

        $dashUser->save();

        return redirect("dash/users/all");
    }
}
