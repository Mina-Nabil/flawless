<?php

namespace App\Http\Controllers;

use App\Models\DashUser;
use App\Models\FollowUp;
use App\Models\Patient;
use App\Models\Room;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FollowupsController extends Controller
{
    public function index()
    {
        $branch_ID = Session::get('branch');
        $this->data['rooms']    =   Room::byBranch($branch_ID)->get();
        $yesterday = ((new DateTime())->sub(new DateInterval('P1D')))->format('Y-m-d');
        //show unconfirmed attendace
        $this->data['items'] = FollowUp::getFollowupsData($branch_ID, "New");
        $this->data['title']            =   'Followup Sheet';
        $this->data['cardTitle']        =   'Pending Patients Follow-ups';
        $this->data['cardSubtitle']        =   'Showing all Pending Follow-ups';
        $this->data['setFollowupsURL'] =   'followups/set/state';

        $this->data['canCall'] = true;
        $this->data['states']           = FollowUp::$STATES;

        return view("followups.table", $this->data);
    }

    public function setFollowup(Request $request)
    {

        $request->validate([
            "id"                    =>  "required",
            "status"                =>  "required",
        ]);

        $followup = FollowUp::findOrFail($request->id);

        if ($request->status && $request->status != "false") {
            if (
                $followup->setCalled(FollowUp::SATISFIED, $request->comment)
            )
                return 1;
        } else {
            if ($followup->setCalled(FollowUp::NOT_SATISFIED, $request->comment)) {
                return 1;
            }
        }
        return 0;
    }

    //Query

    public function loadQuery(Request $request)
    {

        $request->validate([
            "from" => "required",
            "to" => "required",
        ]);
        $branch_ID = Session::get('branch');

        $this->data['items'] = FollowUp::getFollowupsData($request->branchID, $request->state, $request->from, $request->to, $request->caller);

        $this->data['title']        =   'Followups Sheet Report';
        $this->data['cardTitle']    =   'Followups';
        $this->data['cardSubtitle'] =   'Showing Followups from ' . $request->from . ' to ' . $request->to;

        $this->data['canCall']        =   false;
        $this->data['showCaller']    =   true;
        $this->data['setFollowupsURL'] =   'followups/set/state';
        $this->data['states']           = FollowUp::$STATES;

        return view("followups.table", $this->data);
    }

    public function prepareQuery()
    {
        $this->data['title']            =   'Load Follow-ups Sheet';
        $this->data['formTitle']        =   'Follow-up Query';
        $this->data['formSubtitle']     =   'Filter Follow-ups report by';
        // $this->data['patients']         =   Patient::all(); loaded by default
        $this->data['admins']           =   DashUser::admins();
        $this->data['states']           = FollowUp::$STATES;

        return view("followups.query", $this->data);
    }

    //CRUD

    public function insert(Request $request)
    {
        $request->validate([
            "branchID" =>  "required",
            "patientID" =>  "required",
            "date"      =>  "required"
        ]);

        return FollowUp::createFollowup($request->branchID, $request->sessionID, $request->date, $request->comment);
    }

    public function delete($id)
    {
        $followup = FollowUp::findOrFail($id);
        $followup->delete();
    }
}
