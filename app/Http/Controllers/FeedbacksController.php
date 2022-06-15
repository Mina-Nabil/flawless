<?php

namespace App\Http\Controllers;

use App\Models\DashUser;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session as HttpSession;

class FeedbacksController extends Controller
{
    public function index()
    {
        //show unconfirmed attendace
        $branchID = HttpSession::get('branch');
        $this->data['items'] = Feedback::getFeedbackData($branchID, "New", date('Y-m-d', 0), date('Y-m-d'));
        $this->data['title']            =   'Feedback Sheet';
        $this->data['cardTitle']        =   'Pending Patients Feedbacks';
        $this->data['cardSubtitle']        =   'Showing all Pending Feedbacks';
        $this->data['setFeedbackURL'] =   'feedbacks/set/state';

        $this->data['canCall'] = true;
        $this->data['showCaller'] = false;

        return view("feedbacks.table", $this->data);
    }

    public function setFeedback(Request $request)
    {

        $request->validate([
            "id"                    =>  "required",
            "status"                =>  "required",
            "overall"               =>  "required_if:state,Called",
        ]);

        $feedback = Feedback::findOrFail($request->id);

        $status = ($request->status && $request->status == "true") ? "Called" : "Cancelled";

        return $feedback->setCalled($status, $request->overall, $request->comment);
    }

    //Query

    public function loadQuery(Request $request)
    {

        $request->validate([
            "branchID" => "required",
            "from" => "required",
            "to" => "required",
        ]);

        $this->data['items'] = Feedback::getFeedbackData($request->branchID, $request->state, $request->from, $request->to, $request->caller);

        $this->data['title']        =   'Feedbacks Sheet Report';
        $this->data['cardTitle']    =   'Feedbacks';
        $this->data['cardSubtitle'] =   'Showing Feedbacks from ' . $request->from . ' to ' . $request->to;

        $this->data['canCall']        =   false;
        $this->data['showCaller']    =   true;
        return view("feedbacks.table", $this->data);
    }

    public function prepareQuery()
    {
        $this->data['title']            =   'Load Follow-ups Sheet';
        $this->data['formTitle']        =   'Follow-up Query';
        $this->data['formSubtitle']     =   'Filter Follow-ups report by';
        // $this->data['patients']         =   Patient::all(); loaded by default
        $this->data['admins']           =   DashUser::admins();

        return view("feedbacks.query", $this->data);
    }

    //CRUD

    public function insert(Request $request)
    {
        $request->validate([
            "branchID" =>  "required",
            "sessionID" =>  "required",
            "date"      =>  "required"
        ]);

        return Feedback::createFeedback($request->branchID, $request->sessionID, $request->date, $request->comment);
    }

    public function delete($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->delete();
    }
}
