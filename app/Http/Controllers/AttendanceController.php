<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session as HttpSession;

class AttendanceController extends Controller
{

    public function index()
    {
        //show unconfirmed attendace
        $branchID = HttpSession::get('branch');
        $this->data['items']        = Attendance::getAttendanceData($branchID, "New");
        $this->data['title']        =   'Attendance Sheet';
        $this->data['cardTitle']    =   'Unconfirmed Attendance';
        $this->data['cardSubtitle'] =   'Show Unconfirmed Attendance records';
        $this->data['setAttendanceURL'] =   'attendance/set/state';

        $this->data['canChange'] = true;
        $this->data['showConfirmed'] = false;

        return view("attendance.table", $this->data);
    }

    public function loadQuery(Request $request)
    {

        $request->validate([
            "branchID" => "required",
            "from" => "required",
            "to" => "required",
        ]);

        $this->data['items'] = Attendance::getAttendanceData($request->branchID, $request->type, $request->from, $request->to, $request->doctor);

        $this->data['title']        =   'Attendance Sheet Report';
        $this->data['cardTitle']    =   'Attendance';
        $this->data['cardSubtitle'] =   'Showing attendance from ' . $request->from . ' to ' . $request->to;

        $this->data['canChange']        =   false;
        $this->data['showConfirmed']    =   true;
        return view("attendance.table", $this->data);
    }

    public function prepareQuery()
    {
        $this->data['title']            =   'Load Attendance Sheet';
        $this->data['formTitle']            =   'Attendance Query';
        $this->data['formSubtitle']            =   'Filter Attendance report by';
        return view("attendance.query", $this->data);
    }

    public function setAttendance(Request $request)
    {
        $request->validate([
            "id"        =>  "required|exists:attendance,id",
            "status"    =>  "required"
        ]);

        $attendace = Attendance::findOrFail($request->id);

        return $attendace->setAttendance($request->status, $request->shifts, $request->comment);

        // return redirect("attendance/home");
    }

    public function addAttendance(Request $request)
    {
        $request->validate([
            "branchID"  =>  "required|exists:branches,id",
            "doctorID"  =>  "required",
            "date"      =>  "required"
        ]);

        return Attendance::createAttendance($request->doctorID, $request->date, $request->comment, $request->shifts);
    }
}
