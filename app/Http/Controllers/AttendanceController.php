<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{

    public function index()
    {
        //show unconfirmed attendace
        $this->loadAttendanceData("New");
        $this->data['title']            =   'Attendance Sheet';
        $this->data['cardTitle']        =   'Unconfirmed Attendance';
        $this->data['cardSubtitle']        =   'Show Unconfirmed Attendance records';
        $this->data['setAttendanceURL'] =   'attendance/set/state';

        $this->data['canChange'] = true;
        $this->data['showConfirmed'] = false;

        return view("attendance.table", $this->data);
    }

    public function loadQuery(Request $request)
    {

        $request->validate([
            "from" => "required",
            "to" => "required",
        ]);

        $this->loadAttendanceData($request->status, $request->from, $request->to, $request->doctor);

        $this->data['title']        =   'Attendance Sheet Report';
        $this->data['cardTitle']    =   'Attendance';
        $this->data['cardSubtitle'] =   'Showing attendance from ' . $request->from . ' to ' . $request->to ;

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

        $attendace->setAttendance($request->status, $request->comment);

        return redirect("attendance/home");
    }

    public function addAttendance(Request $request)
    {
        $request->validate([
            "doctorID"  =>  "required",
            "date"      =>  "required"
        ]);

        return Attendance::createAttendance($request->doctorID, $request->date, Auth::user()->isAdmin());
    }

    private function loadAttendanceData($type = null, $from = null, $to = null, $doctor = null)
    {
        $query = Attendance::with('doctor');
        if (!is_null($type)) {
            $query = $query->where("ATND_STTS", $type);
        }

        if (!is_null($doctor)) {
            $query = $query->where("ATND_DCTR_ID", $doctor);
        }

        if (!is_null($from) && !is_null($to)) {
            $query = $query->whereBetween("ATND_DATE", [
                $from, $to
            ]);
        }
        $this->data['items'] = $query->get();
    }
}
