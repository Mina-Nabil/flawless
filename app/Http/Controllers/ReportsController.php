<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Channel;
use App\Models\DashUser;
use App\Models\Device;
use App\Models\Location;
use App\Models\Patient;
use App\Models\Session;
use App\Models\SessionItem;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;

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

        $this->data['sessions']         =   Session::getSessions(0, null, 'asc', ['Done'], $startDate, $endDate, null, $request->doctorID, null, null, null, null, true, true);
        $this->data['totalPaid']        =   Session::getDoctorSum($startDate, $endDate, $doctor->id);
        $this->data['sessionsCount']    =   $this->data['sessions']->count();

        $this->data['attendance']       =   Attendance::getAttendanceData("NotCancelled", $startDate, $endDate, $doctor->id);
        $this->data['totalShifts']    =   $this->data['attendance']->sum("ATND_SHFT");

        //table info
        $this->data['title'] = "FLAWLESS Dashboard";
        $this->data['tableTitle'] = "Doctors Report";
        $this->data['tableSubtitle'] = "Showing Doctor {$doctor->DASH_USNM} Report starting from " . (new DateTime($request->from))->format('d-M-Y') . " to " . (new DateTime($request->to))->format('d-M-Y');

        return view("doctors.report", $this->data);
    }
    public function prepareDoctorServicesQuery()
    {
        //page info
        $this->data['title']            =   'Load Doctor Report';
        $this->data['formTitle']            =   'Doctors Performance Query per Device';
        $this->data['formSubtitle']            =   'Set Dates';
        $this->data['devices']      =   Device::all();
        return view("doctors.servicesQuery", $this->data);
    }

    public function loadDoctorServicesData(Request $request)
    {
        $request->validate([
            "from"      =>  "required",
            "to"        =>  "required",
            "doctorID"  =>  "required|exists:dash_users,id"
        ]);

        $startDate = $request->from;
        $endDate = $request->to;
        $doctor = DashUser::findOrFail($request->doctorID);

        $this->data['services']         =   SessionItem::getServicesDoneByDoctor($request->device_ids, $request->doctorID, $request->from, $request->to);
        $this->data['totalPaid']        =   $this->data['services']->sum('discounted_total');
        $this->data['servicesCount']    =   $this->data['services']->count();

        $this->data['attendance']       =   Attendance::getAttendanceData("NotCancelled", $startDate, $endDate, $doctor->id);
        $this->data['totalShifts']    =   $this->data['attendance']->sum("ATND_SHFT");

        //table info
        $this->data['title'] = "FLAWLESS Dashboard";
        $this->data['tableTitle'] = "Doctors Report";
        $this->data['tableSubtitle'] = "Showing Doctor {$doctor->DASH_USNM} Services Report starting from " . (new DateTime($request->from))->format('d-M-Y') . " to " . (new DateTime($request->to))->format('d-M-Y');

        return view("doctors.servicesReport", $this->data);
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
            "branchID"  =>  "required|numeric",
            "from"      =>  "required",
            "to"        =>  "required",
        ]);

        $this->data['sessions'] = Session::getSessions($request->branchID, null, "asc", [Session::STATE_DONE], $request->from, $request->to);

        //table info
        $this->data['from'] = (new DateTime($request->from))->format('d-M-Y');
        $this->data['to'] = (new DateTime($request->to))->format('d-M-Y');

        $this->data['title'] = "FLAWLESS Dashboard";
        $this->data['tableTitle'] = "Sessions Table";
        $this->data['tableSubtitle'] = "Showing sessions and totals from " . (new DateTime($request->from))->format('d-M-Y') . " to " . (new DateTime($request->to))->format('d-M-Y');


        $this->data['discountTotal'] = $this->data['sessions']->sum('discount');
        $this->data['sessionsTotal'] = $this->data['sessions']->sum('total'); //- $this->data['discountTotal'];
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

    public function prepareDevicesRevenue()
    {
        //page info
        $this->data['title']           =   'Revenue Report';
        $this->data['formTitle']       =   'Load Device Revenue Total';
        $this->data['formSubtitle']    =   'Calculate Revenue from Start Date till End Date';

        $this->data['devices']         =   Device::all();
        $this->data['getDeviceTotal']  =   url("reports/devices");

        return view("accounts.device", $this->data);
    }

    public function loadDevicesRevenue(Request $request)
    {
        $request->validate([
            "branchID" => "required|numeric",
            "deviceID" => "required|exists:devices,id",
            "from"  => "required",
            "to"    =>  "required"
        ]);
        $res = SessionItem::getDeviceTotal($request->branchID, $request->deviceID, $request->from, $request->to);
        return json_encode((object) ["total" => $res]);
    }

    public function prepareMissingPatients()
    {
        //page info
        $this->data['title']           =   'Patients Report';
        $this->data['formTitle']       =   'Load All Missing Patients since the provided days';
        $this->data['formSubtitle']    =   'Search for patients who didn\'t visit since the supplied days';

        return view("patients.loadMissing", $this->data);
    }

    public function loadMissingPatients(Request $request)
    {
        $request->validate([
            "daysFrom"      =>  "required",
            "daysTo"      =>  "required",
        ]);
        $this->data['items'] = Patient::loadMissingPatients($request->daysFrom, $request->daysTo);
        //table info
        $this->data['title'] = "FLAWLESS Dashboard";
        $this->data['tableTitle'] = "Missing Patients Report";
        $this->data['tableSubtitle'] = "Showing Patients who didn't visit {$request->days} days ago";

        $this->data['cols'] = ['Code', 'Full Name', 'Mob#', 'Balance', 'Address', 'Since', "Sessions"];
        $this->data['atts'] = [
            'id',
            ['attUrl' => ["url" => 'patients/profile', "urlAtt" => 'id', "shownAtt" =>  "PTNT_NAME"]],
            'PTNT_MOBN',
            ['number' => ['att' => 'PTNT_BLNC']],
            ['comment' => ['att' => 'PTNT_ADRS']],
            ['date' => ['att' => 'created_at', 'format' => 'Y-M-d']],
            'sessionCount',
        ];

        return view("layouts.table", $this->data);
    }

    public function preparePatientsByBranch()
    {
        //page info
        $this->data['title']           =   'Patients Report';
        $this->data['formTitle']       =   'Load Patients by branch';
        $this->data['formSubtitle']    =   'Search for patients by their first visit to one of our branches';

        return view("patients.loadByBranch", $this->data);
    }

    public function loadPatientsByBranch(Request $request)
    {
        $request->validate([
            "branchID"      =>  "required",
        ]);
        $branch = Branch::findOrFail($request->branchID);
        $from = $request->from ? new Carbon($request->from) :  null;
        $to = $request->to ? new Carbon($request->to) :  null;
        $this->data['items'] = Patient::loadByBranch($request->branchID, $from, $to);
        //table info
        $this->data['title'] = "FLAWLESS Dashboard";
        $this->data['tableTitle'] = "Missing Patients Report";
        $this->data['tableSubtitle'] = "Showing Patients who visited {$branch->BRCH_NAME} first";

        $this->data['cols'] = ['Code', 'Full Name', 'Mob#', 'Balance', 'Address', 'Since', "Sessions"];
        $this->data['atts'] = [
            'id',
            ['attUrl' => ["url" => 'patients/profile', "urlAtt" => 'id', "shownAtt" =>  "PTNT_NAME"]],
            'PTNT_MOBN',
            ['number' => ['att' => 'PTNT_BLNC']],
            ['comment' => ['att' => 'PTNT_ADRS']],
            ['date' => ['att' => 'created_at', 'format' => 'Y-M-d']],
            'sessionCount',
        ];

        return view("layouts.table", $this->data);
    }

    public function prepareTopPayers()
    {
        //page info
        $this->data['title']           =   'Top Payers Report';
        $this->data['formTitle']       =   'Load All Patients Paying more than a payment limit';
        $this->data['formSubtitle']    =   'Set a money limit to load all patients paying more than that limit!';

        return view("patients.loadTopPayers", $this->data);
    }

    public function loadTopPayers(Request $request)
    {
        $request->validate([
            "totalPaid"      =>  "required",
        ]);
        $this->data['items'] = Patient::getTopPayers($request->totalPaid);
        //table info
        $this->data['title'] = "FLAWLESS Dashboard";
        $this->data['tableTitle'] = "Top Payers Report";
        $this->data['tableSubtitle'] = "Showing Patients who paid more than " . number_format($request->totalPaid);

        $this->data['cols'] = ['Code', 'Full Name', 'Mob#', "Sessions", "Total", 'Since'];
        $this->data['atts'] = [
            'id',
            ['attUrl' => ["url" => 'patients/profile', "urlAtt" => 'id', "shownAtt" =>  "PTNT_NAME"]],
            'PTNT_MOBN',
            'sessions_count',
            ["number" => ["att" => "total_paid"]],
            ['date' => ['att' => 'created_at', 'format' => 'Y-M-d']],
        ];

        return view("layouts.table", $this->data);
    }

    public function prepareNewPatients()
    {
        //page info
        $this->data['title']           =   'New Patients Report';
        $this->data['formTitle']       =   'Load New Patients';
        $this->data['formSubtitle']    =   'Set start and end date and load all patients created during that time';

        return view("patients.loadNewPatients", $this->data);
    }

    public function loadNewPatients(Request $request)
    {
        $request->validate([
            "from"      =>  "required",
            "to"      =>  "required",
        ]);
        $this->data['items'] = Patient::getPatientsByDate($request->from, $request->to);
        //table info
        $this->data['title'] = "FLAWLESS Dashboard";
        $this->data['tableTitle'] = "New Patients Report";
        $this->data['tableSubtitle'] = "Showing " . $this->data['items']->count() . " Patients who are created from " . (new DateTime($request->from))->format("d M Y") . " to " . (new DateTime($request->to))->format("d M Y");

        $this->data['cols'] = ['Code', 'Full Name', 'Mob#', "Sessions", "Total", 'Since'];
        $this->data['atts'] = [
            'id',
            ['attUrl' => ["url" => 'patients/profile', "urlAtt" => 'id', "shownAtt" =>  "PTNT_NAME"]],
            'PTNT_MOBN',
            'sessions_count',
            ["number" => ["att" => "total_paid"]],
            ['date' => ['att' => 'created_at', 'format' => 'Y-M-d']],
        ];

        return view("layouts.table", $this->data);
    }

    public function preparePatients()
    {
        $this->data['title']           =   'Patients';
        $this->data['formTitle']       =   'Main Patients Report';
        $this->data['formSubtitle']    =   'Load Patients by Channels, Locations';

        $this->data['channels']    =   Channel::all();
        $this->data['locations']    =   Location::all();
        return view("patients.loadReport", $this->data);
    }

    public function loadPatients(Request $request)
    {
        $request->validate([
            "channel_ids"    =>  "required",
            "location_ids"   =>  "required"
        ]);
        $this->data['items'] = Patient::loadBy($request->channel_ids, $request->location_ids, $request->from, $request->to)->with('channel', 'location')->get();
        //table info
        $this->data['title'] = "FLAWLESS Dashboard";
        $this->data['tableTitle'] = "Main Patients Report";
        $this->data['tableSubtitle'] = "Showing " . $this->data['items']->count() . " Patients ";

        $this->data['cols'] = ['Code', 'Location', 'Channel', 'Full Name', 'Mob#', "Sessions", "Total", 'Since'];
        $this->data['atts'] = [
            'id',
            ['foreign' => ['rel' => 'location', 'att' => 'LOCT_NAME']],
            ['foreign' => ['rel' => 'channel', 'att' => 'CHNL_NAME']],
            ['attUrl' => ["url" => 'patients/profile', "urlAtt" => 'id', "shownAtt" =>  "PTNT_NAME"]],
            'PTNT_MOBN',
            'sessions_count',
            ["number" => ["att" => "total_paid"]],
            ['date' => ['att' => 'created_at', 'format' => 'Y-M-d']],
        ];

        return view("layouts.table", $this->data);
    }
}
