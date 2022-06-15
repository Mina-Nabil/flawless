<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Attendance;
use App\Models\Cash;
use App\Models\DashUser;
use App\Models\Device;
use App\Models\Feedback;
use App\Models\FollowUp;
use App\Models\Patient;
use App\Models\Session;
use App\Models\Visa;
use App\Providers\SessionClosed;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session as HttpSession;

class SessionsController extends Controller
{

    //home pages
    public function index($items = null)
    {
        $branch_ID = HttpSession::get('branch');
        //title
        $this->data['title'] = "FLAWLESS Dashboard";

        //dates
        $startOfMonth = (new DateTime())->format('Y-m-01');
        $startOfLast2Month = (new DateTime())->sub(new DateInterval("P2M"))->format('Y-m-01');
        $endOfMonth = (new DateTime())->format('Y-m-t');

        //counts
        $this->data['newSessionsCount']      = Session::getNewCount($branch_ID, $startOfLast2Month, $endOfMonth);
        $this->data['doneSessionsCount']     = Session::getDoneCount($branch_ID, $startOfMonth, $endOfMonth);
        $this->data['pendingPaymentCount']  = Session::getPendingPaymentCount($branch_ID);
        $this->data['todaySessionsCount']    = Session::getTodaySessionsCount($branch_ID);

        //attendance count
        $this->data['unconfirmedCount'] = Attendance::getUnconfirmedCount($branch_ID);
        //followups count
        $this->data['followupsCount'] = FollowUp::getUnconfirmedCount($branch_ID);
        //followups count
        $this->data['feedbacksCount'] = Feedback::getUnconfirmedCount($branch_ID);

        //cash data
        $this->data['paidToday'] = Cash::paidToday($branch_ID);
        $this->data['cashIn'] = Cash::collectedToday($branch_ID);
        $this->data['visaIn'] = Visa::collectedToday($branch_ID);
        $this->data['collectedToday'] = $this->data['cashIn'];
        $this->data['cashBalance'] = Cash::currentBalance($branch_ID);


        //followups

        //attendance


        if ($items == null || $items == "new") {
            $this->data['sessions'] = Session::getNewSessions($branch_ID, $startOfLast2Month, $endOfMonth);
        } else if ($items == "today") {
            $this->data['sessions'] = Session::getTodaySessions($branch_ID);
        } else if ($items == "pending") {
            $this->data['sessions'] = Session::getPendingPaymentSessions($branch_ID);
        } else if ($items == "done") {
            $this->data['sessions'] = Session::getDoneSessions($branch_ID, $startOfMonth, $endOfMonth);
        }

        //Urls
        $this->data['discountURL']    = url('sessions/set/discount');
        $this->data['paymentURL']    = url('sessions/add/payment');
        $this->data['editSessionURL']    = url('sessions/edit');

        $this->data['addPaymentModalURL']    = url('payments/modal/add');
        $this->data['newSessionsURL']    = url('sessions/show/new');
        $this->data['todaySessionsURL']    = url('sessions/show/today');
        $this->data['pendingSessionsURL']    = url('sessions/show/pending');
        $this->data['doneSessionsURL']    = url('sessions/show/done');


        return view("home", $this->data);
    }

    public function calendar()
    {
        $branch_ID = HttpSession::get('branch');
        $this->data['title']    =   "Calendar";
        $startOfYear = (new DateTime())->format('Y-01-01');
        $endOfYear = (new DateTime())->format('Y-12-31');
        $this->data['sessions'] =   Session::getSessions($branch_ID, 'desc', null, $startOfYear, $endOfYear);
        return view('layouts.calendar', $this->data);
    }

    //////Single Session Functions
    public function details($id)
    {

        $this->data['session'] = Session::with(["logs" => function ($query) {
            $query->orderBy('id', 'desc');
        }], "items", "patient", "doctor", "creator", "items.pricelistItem", "items.pricelistItem.device", "items.pricelistItem.area", "logs.user")->findOrFail($id);
        $this->data['patient'] = Patient::with("sessions", "services", "services.session", "services.session.doctor", "services.pricelistItem", "services.pricelistItem.device", "services.pricelistItem.area")->findOrFail($this->data['session']->SSHN_PTNT_ID);

        $this->data['patient_packages'] = $this->data['patient']->available_packages;

        //Services Table
        $this->data['servicesList']    =   $this->data['patient']->services;
        $this->data['cardTitle'] = false;
        $this->data['servicesCols'] = ['Date', 'Session#', 'Doctor', 'Device',  'Type', 'Area', 'Unit', 'Comment'];
        $this->data['servicesAtts'] = [
            ['foreignDate' => ['rel' => 'session', 'att' => 'SSHN_DATE', 'format' => 'd-M-Y']],
            ['attUrl' => ['url' => "sessions/details", "shownAtt" => 'SHIT_SSHN_ID', "urlAtt" => 'SHIT_SSHN_ID']],
            ['foreignForeign' => ['rel1' => 'session', 'rel2' => 'doctor', 'att' => 'DASH_USNM']],
            ['foreignForeign' => ['rel1' => 'pricelistItem', 'rel2' => 'device', 'att' => 'DVIC_NAME']],
            ['foreign' => ['rel' => 'pricelistItem', 'att' => 'PLIT_TYPE']],
            ['foreignForeign' => ['rel1' => 'pricelistItem', 'rel2' => 'area', 'att' => 'AREA_NAME']],
            'SHIT_QNTY',
            ['comment' => ['att' => "SHIT_NOTE"]]
        ];

        //page data
        $this->data['title']    = "Session Details";
        // $this->data['patients'] = Patient::all(); added by default
        $this->data['devices']  = Device::all();
        $this->data['areas']    = Area::all();
        $this->data['doctors']    = DashUser::where("DASH_TYPE_ID", 2)->with('dash_types')->get();

        //URLs
        $this->data['manageServicesURL']        = "sessions/manage/items";
        $this->data['paymentURL']               = "sessions/add/payment";
        $this->data['doctorURL']               = "sessions/set/doctor";
        $this->data['discountURL']              = "sessions/set/discount";
        $this->data['editSessionURL']           = "sessions/edit";
        $this->data['deleteSessionURL']           = "sessions/delete/" .  $this->data['session']->id;
        $this->data['settleSessionOnBalanceURL'] = url('sessions/settle/balance/' . $this->data['session']->id);
        $this->data['settleSessionOnPackagesURL'] = url('sessions/settle/packages/' . $this->data['session']->id);

        $this->data['setSessionPendingUrl']     = "sessions/set/pending/" . $this->data['session']->id;
        $this->data['setSessionDoneUrl']        = "sessions/set/done/" . $this->data['session']->id;
        $this->data['setSessionNewUrl']         = "sessions/set/new/" . $this->data['session']->id;
        $this->data['setSessionCancelledUrl']   = "sessions/set/cancelled/" . $this->data['session']->id;
        $this->data['updateServicesURL']           = "sessions/update/services";
        $this->data['getServicesAPI']           = "sessions/api/get/services";


        return view("sessions.details", $this->data);
    }

    public function manageServices(Request $request)
    {
        $request->validate([
            "id" => "required"
        ]);
        $session =  Session::findOrFail($request->id);
        $session->clearServices();
        if (isset($request->service))
            foreach ($request->service as $key => $pricelistID) {
                $session->addService($pricelistID, $request->unit[$key], $request->note[$key], false, $request->isDoctor[$key] == "on" ? true : false, $request->isCollected[$key]);
            }

        if (isset($request->isCommission) && $request->isCommission == "on")
            $session->setCommission(true);
        else
            $session->setCommission(false);

        $session->calculateTotal();

        if (!isset($session->doctor) && (Auth::user())->isDoctor())
            $session->assignTo(Auth::user()->id);

        return $this->redirectToDetails($session->id);
    }

    public function settleBalance($sessionID)
    {

        $session = Session::findOrFail($sessionID);
        $session->payFromPatientBalance();

        return $this->redirectToDetails($session->id);
    }

    public function settlePackages($sessionID)
    {

        $session = Session::findOrFail($sessionID);
        $session->payFromPatientPackages();

        return $this->redirectToDetails($session->id);
    }

    public function acceptPayment(Request $request)
    {
        $request->validate([
            "id" => "required",
            "amount" => "required"
        ]);

        $session = Session::findOrFail($request->id);
        $isCash = $request->cashRadio == "cash";
        $session->addPayment($request->amount, $isCash);

        return $this->redirectToDetails($session->id);
    }

    public function setDiscount(Request $request)
    {
        $request->validate([
            "id" => "required",
            "discount" => "required"
        ]);

        $session = Session::findOrFail($request->id);
        $session->setDiscount($request->discount);

        return $this->redirectToDetails($session->id);
    }

    public function setDoctor(Request $request)
    {
        $request->validate([
            "id" => "required",
            "doctorID" => "required"
        ]);

        $session = Session::findOrFail($request->id);
        $session->assignTo($request->doctorID);

        return $this->redirectToDetails($session->id);
    }

    //Sessions Queries
    public function prepareQuery()
    {
        //page info
        $this->data['title']            =   'Load Sessions Report';
        $this->data['formTitle']            =   'Sessions Query';
        $this->data['formSubtitle']            =   'Filter Sessions report by';

        //filters data
        // $this->data['patients'] = Patient::all(); added by default in Controller
        $this->data['admins']   = DashUser::admins();
        $this->data['sessionsMin'] = Session::getMinTotal();
        $this->data['sessionsMax'] = Session::getMaxTotal();

        return view("sessions.query", $this->data);
    }

    public function loadQuery(Request $request)
    {
        $request->validate([
            "branchID"  =>  "required|numeric",
            "from"  =>  "required",
            "to"    =>  "required"
        ]);

        //query
        $this->data['items'] = Session::getSessions($request->branchID, "asc", $request->state, $request->from, $request->to, $request->patient, $request->doctor, $request->opener, $request->moneyMan, $request->totalBegin, $request->totalEnd, $request->isCommission);

        $this->data['cols'] = ["Date", "Doctor", "Patient", "Status", "CreatedBy", "Total", "Disc.", "Paid To", "Comment"];

        $this->data['atts'] = [
            ["date"         =>  ["att"  =>  "SSHN_DATE", "format" => "d-M-Y"]],
            ["verifiedRel"  =>  ["rel"  =>  "doctor",   "relAtt"   =>  "DASH_USNM", 'isVerified' => 'SSHN_CMSH', 'iconTitle' => "Commission"]],
            ["foreign"      =>  ["rel"  =>  "patient",  "att"   =>  "PTNT_NAME"]],
            [
                'state'     => [
                    "url"       => "sessions/details/",
                    "urlAtt"    =>  "id",
                    "att"   =>  "SSHN_STTS",
                    "text" => [
                        "New" => "New",
                        "Pending Payment" => "Pending Payment",
                        "Cancelled" => "Cancelled",
                        "Done" => "Done",
                    ],
                    "classes" => [
                        "New" => "label-info",
                        "Pending Payment" => "label-dark",
                        "Cancelled" => "label-danger",
                        "Done" => "label-success",
                    ],
                ]
            ],
            ["foreign"  =>  ["rel"  =>  "creator",  "att"   =>  "DASH_USNM"]],
            ["number"   =>  ["att"  =>  "SSHN_TOTL"]],
            ["number"   =>  ["att"  =>  "SSHN_DISC"]],
            ["foreign"  =>  ["rel"  =>  "accepter",  "att"   =>  "DASH_USNM"]],
            ["comment"  =>  ["att"  =>  "SSHN_TEXT"]],

        ];

        //table info
        $this->data['title'] = "FLAWLESS Dashboard";
        $this->data['tableTitle'] = "Sessions Report";
        $this->data['totalSum'] = $this->data['items']->sum('SSHN_TOTL');
        $this->data['totalDisc'] = $this->data['items']->sum('SSHN_DISC');
        $this->data['totalDiff'] = $this->data['totalSum'] - $this->data['totalDisc'];
        $this->data['tableSubtitle'] = "Showing sessions from " . (new DateTime($request->from))->format('d-M-Y') . " to " . (new DateTime($request->to))->format('d-M-Y') . ' -- ' .
            'Total Sum: ' . number_format($this->data['totalSum']) . ' Total Discount: ' . number_format($this->data['totalDisc']) . '   (Sum-Disc): ' . number_format($this->data['totalDiff']);

        return view('layouts.table', $this->data);
    }

    //////Set States

    public function setSessionPending($id)
    {
        $session = Session::findOrFail($id);
        $session->setAsPendingPayment();
        return $this->redirectToDetails($session->id);
    }

    public function setSessionNew($id)
    {
        $session = Session::findOrFail($id);
        $session->setAsNew();
        return $this->redirectToDetails($session->id);
    }

    public function setSessionDone($id, $date = null)
    {
        $session = Session::findOrFail($id);
        $session->setAsDone($date);
        SessionClosed::dispatch($session);
        return $this->redirectToDetails($session->id);
    }

    public function setSessionCancelled($id)
    {
        $session = Session::findOrFail($id);
        $session->setAsCancelled();
        return $this->redirectToDetails($session->id);
    }

    ///////CRUD
    public function insert(Request $request)
    {
        $request->validate([
            "branchID" =>  "required|numeric",
            "patientID" =>  "required|exists:patients,id",
            "sesDate"      =>  "required|date",
            "start"     =>  "required",
            "end"       =>  "required|after:start",
        ]);

        echo Session::createNewSession($request->branchID, $request->patientID, $request->sesDate, $request->start, $request->end, $request->comment);
    }

    public function edit(Request $request)
    {
        $request->validate([
            "id"        =>  "required",
            "patientID" =>  "required|exists:patients,id",
            "sessionDate"   =>  "required|date",
            "sessionStartTime"     =>  "required",
            "sessionEndTime"       =>  "required|after:start",
        ]);

        $session = Session::findOrFail($request->id);

        $session->SSHN_PTNT_ID = $request->patientID;
        $session->SSHN_DATE = $request->sessionDate;
        $session->SSHN_STRT_TIME = $request->sessionStartTime;
        $session->SSHN_END_TIME = $request->sessionEndTime;
        $session->SSHN_TEXT = $request->sessionComment;

        $session->save();

        return redirect("sessions/details/" . $session->id);
    }

    public function delete($sessionID)
    {

        $session = Session::findOrFail($sessionID);
        $session->deleteSession();
        return $this->redirectToHome();
    }

    /////API functions
    public function getServices(Request $request)
    {

        $request->validate([
            "deviceID" => "required",
            "patientID" => "required"
        ]);

        $device = Device::findOrFail($request->deviceID);

        return json_encode($device->availableServices($request->patientID), JSON_UNESCAPED_UNICODE);
    }

    //redirects
    private function redirectToDetails($id)
    {
        return redirect('sessions/details/' . $id);
    }
    private function redirectToHome()
    {
        return redirect('/');
    }
}
