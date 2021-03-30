<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Exception;
use Illuminate\Http\Request;

class ChannelsController extends Controller
{
    protected $data;
    //init page data
    private function initDataArr()
    {
        $this->data['items'] = Channel::all();
        $this->data['title'] = "Available Channels";
        $this->data['subTitle'] = "Manage, Add and Delete Patient Channels";
        $this->data['formTitle'] = "Add Channel";
        $this->data['cols'] = ['Name', 'Edit'];
        $this->data['atts'] = ['CHNL_NAME', ['edit' => ['url' => 'channels/edit/', 'att' => 'id']]];
        $this->data['homeURL'] = 'channels/home';
    }

    public function index()
    {

        $this->initDataArr();

        $this->data['formURL'] = "channels/insert";
        $this->data['isCancel'] = false;
        return view("settings.channels", $this->data);
    }

    public function edit($id)
    {
        $this->data['channel'] = Channel::findOrFail($id);
        $this->initDataArr();
        $this->data['formTitle'] = "Manage Channel (" . $this->data['channel']->CHNL_NAME . ')';
        $this->data['formURL'] = "channels/update";
        $this->data['isCancel'] = true;
        return view("settings.channels", $this->data);
    }

    public function insert(Request $request)
    {
        $Channel = new Channel;

        $request->validate([
            'name' => 'required',
        ]);

        $Channel->CHNL_NAME = $request->name;
        $Channel->save();

        return redirect("channels/home");
    }

    public function update(Request $request)
    {

        $request->validate([
            'id' => 'required',
            'name' => 'required'
        ]);

        $Channel = Channel::findOrFail($request->id);

        $Channel->CHNL_NAME = $request->name;

        $Channel->save();

        return redirect("channels/home");
    }

    public function delete($channelID)
    {
        $Channel = Channel::findOrFail($channelID);
        $Channel->UnlinkAnddelete();

        return redirect("channels/home");
    }
}
