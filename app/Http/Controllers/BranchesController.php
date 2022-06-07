<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Exception;
use Illuminate\Http\Request;

class BranchesController extends Controller
{
    protected $data;
    //init page data
    private function initDataArr()
    {
        $this->data['items'] = Branch::all();
        $this->data['title'] = "Available Branches";
        $this->data['subTitle'] = "Manage, Add and Delete Branches";
        $this->data['formTitle'] = "Add Branch";
        $this->data['cols'] = ['Name', 'Edit'];
        $this->data['atts'] = ['BRCH_NAME', ['edit' => ['url' => 'branches/edit/', 'att' => 'id']]];
        $this->data['homeURL'] = 'branches/home';
    }

    public function index()
    {

        $this->initDataArr();

        $this->data['formURL'] = "branches/insert";
        $this->data['isCancel'] = false;
        return view("settings.branches", $this->data);
    }

    public function edit($id)
    {
        $this->data['branch'] = Branch::findOrFail($id);
        $this->initDataArr();
        $this->data['formTitle'] = "Manage Branch (" . $this->data['branch']->BRCH_NAME . ')';
        $this->data['formURL'] = "branches/update";
        $this->data['isCancel'] = true;
        return view("settings.branches", $this->data);
    }

    public function insert(Request $request)
    {
        $Branch = new Branch;

        $request->validate([
            'name' => 'required',
        ]);

        $Branch->BRCH_NAME = $request->name;
        $Branch->save();

        return redirect("branches/home");
    }

    public function update(Request $request)
    {

        $request->validate([
            'id' => 'required',
            'name' => 'required'
        ]);

        $Branch = Branch::findOrFail($request->id);

        $Branch->BRCH_NAME = $request->name;

        $Branch->save();

        return redirect("branches/home");
    }
}
