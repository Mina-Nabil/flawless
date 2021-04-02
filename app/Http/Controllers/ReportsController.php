<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\DashUser;
use App\Models\Session;
use DateTime;
use Illuminate\Http\Request;
use PhpParser\Comment\Doc;

class ReportsController extends Controller
{

    //Doctors Queries
    public function prepareDoctorQuery()
    {
        //page info
        $this->data['title']            =   'Load Doctor Report';
        $this->data['formTitle']            =   'Doctors Performance Query';
        $this->data['formSubtitle']            =   'Set Dates';

        return view("doctors.query", $this->data);
    }

    public function loadDoctorData(Request $request)
    {
        $request->validate([
            "from"      =>  "required",
            "to"        =>  "required",
            "doctorID"  =>  "required|exists:dash_users,id"
        ]);

        $startDate = (new DateTime($request->from))->format('Y-m-d 00:00:00');
        $endDate = (new DateTime($request->from))->format('Y-m-d 23:59:59');
        $doctor = DashUser::findOrFail($request->doctorID);

        $this->data['sessions']         =   Session::getSessions('asc', 'Done', $startDate, $endDate, null, $request->doctorID, null, null, null, null, null, true);
        $this->data['totalPaid']        =   Session::getPaidSum($startDate, $endDate, $doctor->id);
        $this->data['sessionsCount']    =   $this->data['sessions']->count();
        
        $this->data['attendance']       =   Attendance::getAttendanceData("NotCancelled", $startDate, $endDate, $doctor->id);
        $this->data['totalShifts']    =   $this->data['attendance']->sum("ATND_SHFT");

        //table info
        $this->data['title'] = "FLAWLESS Dashboard";
        $this->data['tableTitle'] = "Doctors Report";
        $this->data['tableSubtitle'] = "Showing Doctor {$doctor->DASH_USNM} Report starting from " . (new DateTime($request->from))->format('d-M-Y') . " to " . (new DateTime($request->to))->format('d-M-Y');

        return view("doctors.report", $this->data);
    }
}
