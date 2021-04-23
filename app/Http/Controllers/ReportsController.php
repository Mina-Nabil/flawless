<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\DashUser;
use App\Models\Session;
use DateInterval;
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

        $startDate = $request->from;
        $endDate = $request->to;
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

    //Revenue Query
    public function prepareRevenue()
    {
        //page info
        $this->data['title']            =   'Revenue Report';
        $this->data['formTitle']            =   'Prepare Revenue Query';
        $this->data['formSubtitle']            =   'Calculate Revenue from Start Date till End Date';

        return view("accounts.query", $this->data);
    }

    public function loadRevenue(Request $request)
    {
        $request->validate([
            "from"      =>  "required",
            "to"        =>  "required",
        ]);

        $this->data['sessions'] = Session::getSessions("asc", Session::STATE_DONE, $request->from, $request->to);

        //table info
        $this->data['title'] = "FLAWLESS Dashboard";
        $this->data['tableTitle'] = "Sessions Table";
        $this->data['tableSubtitle'] = "Showing sessions from " . (new DateTime($request->from))->format('d-M-Y') . " to " . (new DateTime($request->to))->format('d-M-Y');


        $this->data['discountTotal'] = $this->data['sessions']->sum('SSHN_DISC');
        $this->data['sessionsTotal'] = $this->data['sessions']->sum('SSHN_TOTL') - $this->data['discountTotal'];
        $this->data['sessionsCount'] = $this->data['sessions']->count();

        //Charts
        $this->data['chartTitle'] = "Revenue Graph";
        $this->data['chartSubtitle'] = "Showing Revenue for the latest 12 months";


   
        $max = 0;
        $sum = 0;
        for ($i = 1; $i < 13; $i++) {
            $monthDate = (new DateTime())->sub(new DateInterval("P" . (12 - $i) . "M"));
            $this->data['graphLabels'][$i] =  $monthDate->format('M-y');
            $from = $monthDate->format('Y-m-01');
            $to = $monthDate->format('Y-m-t');
            $this->data['graphData'][0][$i] = Session::getTotalSum($from, $to);
            $sum += $this->data['graphData'][0][$i];
            $max = max($max, $this->data['graphData'][0][$i]);
        }
       
        $this->data['graphMax'] = $max;

        $this->data['graphTotal'] = [
            ["title" => "Total In", "value" => $sum,],
        ];
        return view('accounts.revenue', $this->data);
    }
}
