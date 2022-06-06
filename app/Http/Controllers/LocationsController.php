<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Exception;
use Illuminate\Http\Request;

class LocationsController extends Controller
{
    protected $data;
    //init page data
    private function initDataArr()
    {
        $this->data['items'] = Location::all();
        $this->data['title'] = "Available Locations";
        $this->data['subTitle'] = "Manage, Add and Delete Patient Locations";
        $this->data['formTitle'] = "Add Location";
        $this->data['cols'] = ['Name', 'Edit'];
        $this->data['atts'] = ['LOCT_NAME', ['edit' => ['url' => 'locations/edit/', 'att' => 'id']]];
        $this->data['homeURL'] = 'locations/home';
    }

    public function index()
    {

        $this->initDataArr();

        $this->data['formURL'] = "locations/insert";
        $this->data['isCancel'] = false;
        return view("settings.locations", $this->data);
    }

    public function edit($id)
    {
        $this->data['location'] = Location::findOrFail($id);
        $this->initDataArr();
        $this->data['formTitle'] = "Manage Location (" . $this->data['location']->LOCT_NAME . ')';
        $this->data['formURL'] = "locations/update";
        $this->data['isCancel'] = true;
        return view("settings.locations", $this->data);
    }

    public function insert(Request $request)
    {
        $Location = new Location;

        $request->validate([
            'name' => 'required',
        ]);

        $Location->LOCT_NAME = $request->name;
        $Location->save();

        return redirect("locations/home");
    }

    public function update(Request $request)
    {

        $request->validate([
            'id' => 'required',
            'name' => 'required'
        ]);

        $Location = Location::findOrFail($request->id);

        $Location->LOCT_NAME = $request->name;

        $Location->save();

        return redirect("locations/home");
    }

    public function delete($locationID)
    {
        $Location = Location::findOrFail($locationID);
        $Location->UnlinkAnddelete();

        return redirect("locations/home");
    }
}
