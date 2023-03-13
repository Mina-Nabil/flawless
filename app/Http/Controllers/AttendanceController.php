<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\DoctorAvailability;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session as HttpSession;

class AttendanceController extends Controller
{


    public function schedule(Request $request)
    {
        /** @var Carbon */
        $defaultStart   = (new Carbon('now'))->startOfWeek();
        /** @var Carbon */
        $defaultEnd    = (new Carbon('now'))->addDays(21);

        //loading filters data
        $this->data['shifts'] = DoctorAvailability::SHIFTS_ARR;

        $this->data['selectedFrom'] = $request->input('from') ?? $defaultStart->format('Y-m-d');
        $this->data['selectedTo'] = $request->input('to') ?? $defaultEnd->format('Y-m-d');
        $this->data['selectedBranch'] = $request->input('branchID');
        $this->data['selectedDoctor'] = $request->input('doctorID');
        $this->data['selectedShift'] = $request->input('shift');

          /** @var Carbon */
          $start   = new Carbon($this->data['selectedFrom']);
          /** @var Carbon */
          $end    = new Carbon($this->data['selectedTo']);

        $this->data['days'] = array();
        $branchesCount = $this->data['branches']->count();
        $this->data['branchesCount'] = $branchesCount;

        while ($start->lessThanOrEqualTo($end)) {
            $dateKey = $start->dayName . " - " . $start->format('Y-m-d');
            $this->data['days'][$dateKey]['shift1'] = ($this->data['selectedShift'] == DoctorAvailability::SHIFT_2) ? [] : DoctorAvailability::getAvailableDoctors($start, DoctorAvailability::SHIFT_1, $this->data['selectedDoctor'], $this->data['selectedBranch']);
            $this->data['days'][$dateKey]['shift2'] = ($this->data['selectedShift'] == DoctorAvailability::SHIFT_1) ? [] :DoctorAvailability::getAvailableDoctors($start, DoctorAvailability::SHIFT_2, $this->data['selectedDoctor'], $this->data['selectedBranch']);
            $this->data['days'][$dateKey]['shift1Count'] =  count($this->data['days'][$dateKey]['shift1']);
            $this->data['days'][$dateKey]['shift1RowSpan'] =  max(1, $this->data['days'][$dateKey]['shift1Count']);
            $this->data['days'][$dateKey]['shift2Count'] =  count($this->data['days'][$dateKey]['shift2']);
            $this->data['days'][$dateKey]['shift2RowSpan'] =  max(1, $this->data['days'][$dateKey]['shift2Count']);
            $this->data['days'][$dateKey]['dayCount'] = $this->data['days'][$dateKey]['shift1Count'] + $this->data['days'][$dateKey]['shift2Count'];
            $this->data['days'][$dateKey]['dayRowSpan'] = $this->data['days'][$dateKey]['shift1RowSpan'] + $this->data['days'][$dateKey]['shift2RowSpan'];

            $start = $start->addDay();
        }
        // dd($this->data['days']);
        $this->data['title'] = "Flawless Schedule";
        return view("attendance.schedule", $this->data);
    }

    public function index()
    {
        //show unconfirmed attendace
        $branchID = HttpSession::get('branch');
        $this->data['rooms']    =   Room::byBranch($branchID)->get();
        $this->data['items']        = Attendance::getAttendanceData("New");
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
            "from" => "required",
            "to" => "required",
        ]);

        $this->data['items'] = Attendance::getAttendanceData($request->type, $request->from, $request->to, $request->doctor);

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
