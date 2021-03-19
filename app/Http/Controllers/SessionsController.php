<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Attendance;
use App\Models\Cash;
use App\Models\DashUser;
use App\Models\Device;
use App\Models\Patient;
use App\Models\Session;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{

    //home pages
    public function index($items = null)
    {

        //title
        $this->data['title'] = "FLAWLESS Dashboard";

        //dates
        $startOfMonth = (new DateTime())->format('Y-m-01');
        $endOfMonth = (new DateTime())->format('Y-m-t');

        //counts
        $this->data['newSessionsCount']      = Session::getNewCount($startOfMonth, $endOfMonth);
        $this->data['doneSessionsCount']     = Session::getDoneCount($startOfMonth, $endOfMonth);
        $this->data['pendingPaymentCount']  = Session::getPendingPaymentCount();
        $this->data['todaySessionsCount']    = Session::getTodaySessionsCount();

        //attendance count
        $this->data['unconfirmedCount'] = Attendance::getUnconfirmedCount();
        
        //followups count
        $this->data['followupsCount'] = 12;

        //cash data
        $this->data['paidToday'] = Cash::paidToday();
        $this->data['collectedToday'] = Cash::collectedToday();

        //followups

        //attendance


        if ($items == null || $items == "new") {
            $this->data['sessions'] = Session::getNewSessions($startOfMonth, $endOfMonth);
        } else if ($items == "today") {
            $this->data['sessions'] = Session::getTodaySessions();
        } else if ($items == "pending") {
            $this->data['sessions'] = Session::getPendingPaymentSessions();
        } else if ($items == "done") {
            $this->data['sessions'] = Session::getDoneSessions($startOfMonth, $endOfMonth);
        }

        //Urls
        $this->data['discountURL']    = url('sessions/set/discount');
        $this->data['paymentURL']    = url('sessions/add/payment');
        $this->data['editSessionURL']    = url('sessions/edit');

        $this->data['newSessionsURL']    = url('sessions/show/new');
        $this->data['todaySessionsURL']    = url('sessions/show/today');
        $this->data['pendingSessionsURL']    = url('sessions/show/pending');
        $this->data['doneSessionsURL']    = url('sessions/show/done');


        return view("home", $this->data);
    }

    //////Single Session Functions
    public function details($id)
    {

        $this->data['session'] = Session::with(["logs" => function ($query) {
            $query->orderBy('id', 'desc');
        }], "items", "patient", "doctor", "creator", "items.pricelistItem", "items.pricelistItem.device", "items.pricelistItem.area", "logs.user")->findOrFail($id);
        $this->data['patient'] = Patient::with("sessions", "services", "services.session", "services.session.doctor", "services.pricelistItem", "services.pricelistItem.device", "services.pricelistItem.area")->findOrFail($this->data['session']->SSHN_PTNT_ID);

        //Services Table
        $this->data['servicesList']    =   $this->data['patient']->services;
        $this->data['cardTitle'] = false;
        $this->data['servicesCols'] = ['Date', 'Session#', 'Doctor', 'Device',  'Type', 'Area', 'Unit'];
        $this->data['servicesAtts'] = [
            ['foreign' => ['rel' => 'session', 'att' => 'SSHN_DATE']],
            ['attUrl' => ['url' => "sessions/details", "shownAtt" => 'SHIT_SSHN_ID', "urlAtt" => 'SHIT_SSHN_ID']],
            ['foreignForeign' => ['rel1' => 'session', 'rel2' => 'doctor', 'att' => 'DASH_USNM']],
            ['foreignForeign' => ['rel1' => 'pricelistItem', 'rel2' => 'device', 'att' => 'DVIC_NAME']],
            ['foreign' => ['rel' => 'pricelistItem', 'att' => 'PLIT_TYPE']],
            ['foreignForeign' => ['rel1' => 'pricelistItem', 'rel2' => 'area', 'att' => 'AREA_NAME']],
            'SHIT_QNTY'
        ];

        //page data
        $this->data['title']    = "Session Details";
        $this->data['patients'] = Patient::all();
        $this->data['devices']  = Device::all();
        $this->data['areas']    = Area::all();
        $this->data['doctors']    = DashUser::where("DASH_TYPE_ID", 2)->with('dash_types')->get();

        //URLs
        $this->data['manageServicesURL']        = "sessions/manage/items";
        $this->data['paymentURL']               = "sessions/add/payment";
        $this->data['doctorURL']               = "sessions/set/doctor";
        $this->data['discountURL']              = "sessions/set/discount";
        $this->data['editSessionURL']           = "sessions/edit";
        $this->data['settleSessionOnBalanceURL'] = url('sessions/settle/balance/' . $this->data['session']->id);

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

        $session = Session::findOrFail($request->id);
        $session->clearServices();
        if (isset($request->service))
            foreach ($request->service as $key => $pricelistID) {
                $session->addService($pricelistID, $request->unit[$key], false);
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

    public function acceptPayment(Request $request)
    {
        $request->validate([
            "id" => "required",
            "amount" => "required"
        ]);

        $session = Session::findOrFail($request->id);
        $isCash = (isset($request->isCash) && $request->isCash == "on");
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

    public function setSessionDone($id)
    {
        $session = Session::findOrFail($id);
        $session->setAsDone();
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
            "patientID" =>  "required|exists:patients,id",
            "sesDate"      =>  "required|date",
            "start"     =>  "required",
            "end"       =>  "required|after:start",
        ]);

        echo Session::createNewSession($request->patientID, $request->sesDate, $request->start, $request->end, $request->comment);
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
}