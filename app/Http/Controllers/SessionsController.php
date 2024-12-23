<?php

namespace App\Http\Controllers;

use App\Helpers\SmsHandler;
use App\Jobs\SendSMSJob;
use App\Models\Area;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Cash;
use App\Models\DashUser;
use App\Models\DayNote;
use App\Models\Device;
use App\Models\Feedback;
use App\Models\FollowUp;
use App\Models\Patient;
use App\Models\PriceListItem;
use App\Models\Room;
use App\Models\Session;
use App\Models\StockItem;
use App\Models\Visa;
use App\Providers\SessionClosed;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session as HttpSession;
use stdClass;

class SessionsController extends Controller
{

    //home pages
    public function index($items = "today")
    {
        $branch_ID = HttpSession::get('branch');
        $this->data['rooms']    =   Room::byBranch($branch_ID)->get();
        //title
        $this->data['title'] = "FLAWLESS Dashboard";

        //dates
        $startOfMonth = (new DateTime())->format('Y-m-01');
        $startOfLast2Month = (new DateTime())->sub(new DateInterval("P2M"))->format('Y-m-01');
        $endOfMonth = (new DateTime())->format('Y-m-t');

        //counts
        /** @var DashUser */
        $user = Auth::user();

        $this->data['newSessionsCount']      = Session::getNewCount($branch_ID, $startOfLast2Month, $endOfMonth);
        $this->data['doneSessionsCount']     = Session::getDoneCount($branch_ID, $startOfMonth, $endOfMonth);
        $this->data['pendingPaymentCount']  = Session::getPendingPaymentCount($branch_ID);
        $this->data['todaySessionsCount']    = Session::getTodaySessionsCount($branch_ID);


        //attendance count
        $this->data['unconfirmedCount'] = Attendance::getUnconfirmedCount();
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

    public function calendar($roomID = null)
    {
        $branch_ID = HttpSession::get('branch');
        $this->data['rooms']    =   Room::byBranch($branch_ID)->get();
        $this->data['title']    =   "Calendar";
        $this->data['branch']    =   Branch::find($branch_ID);
        $this->data['room']    =   Room::find($roomID);
        $startOfYear = (new DateTime())->format('Y-01-01');
        $endOfYear = (new DateTime())->format('Y-12-31');
        $this->data['sessions'] =   Session::getSessions($branch_ID, null, 'desc', [], $startOfYear, $endOfYear);
        return view('layouts.calendar', $this->data);
    }

    //////Single Session Functions
    public function details($id)
    {
        $this->data['session'] = Session::with(["logs" => function ($query) {
            $query->orderBy('id', 'desc');
        }], "items", "patient", "doctor", "creator", "items.pricelistItem", "items.pricelistItem.device", "items.pricelistItem.area", "logs.user", "packageLogs", "room")->findOrFail($id);
        $this->data['patient'] = Patient::with("sessions", "services", "services.session", "services.session.doctor", "services.pricelistItem", "services.pricelistItem.device", "services.pricelistItem.area")->findOrFail($this->data['session']->SSHN_PTNT_ID);

        $this->data['patient_packages'] = $this->data['patient']->available_packages;
        $this->data['stockItems'] = StockItem::active()->session()->get();

        //Services Table
        $this->data['servicesList']    =   $this->data['patient']->services;
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

        $this->data['stockTrans']    =   StockItem::loadTransaction("100" . $this->data['session']->id);
        $this->data['stockCols'] = ['#', 'User', 'Items', 'Total'];
        $this->data['stockAtts'] = [
            ['attUrl' => ['url' => "stock/transaction", "shownAtt" => 'STTR_CODE', "urlAtt" => 'STTR_CODE']],
            'DASH_USNM',
            'STCK_NAME',
            ['number'   =>  ['att'  =>  'STTR_AMNT']]
        ];

        //page data
        /** @var DashUser */
        $user = Auth::user();
        $this->data['title']    = "Session Details";
        // $this->data['patients'] = Patient::all(); added by default

        $this->data['doctors']    = DashUser::where("DASH_TYPE_ID", 2)->with('dash_types')->get();

        //URLs
        $this->data['manageServicesURL']        = "sessions/manage/items";
        $this->data['paymentURL']               = "sessions/add/payment";
        $this->data['stockURL']               = "stock/entry";
        $this->data['doctorURL']               = "sessions/set/doctor";
        $this->data['discountURL']              = "sessions/set/discount";
        $this->data['editSessionURL']           = "sessions/edit";
        $this->data['setNoteURL']           = url("patients/setnote");
        $this->data['deleteNoteURL']           = url("patients/notes/delete");
        $this->data['restoreNoteURL']           = url("patients/notes/restore");
        $this->data['deleteSessionURL']           = "sessions/delete/" .  $this->data['session']->id;
        $this->data['settleSessionOnBalanceURL'] = url('sessions/settle/balance/' . $this->data['session']->id);
        $this->data['settleSessionOnPackagesURL'] = url('sessions/settle/packages/' . $this->data['session']->id);

        $this->data['setSessionPendingUrl']     = "sessions/set/pending/" . $this->data['session']->id;
        $this->data['setSessionDoneUrl']        = "sessions/set/done/" . $this->data['session']->id;
        $this->data['setSessionNewUrl']         = "sessions/set/new/" . $this->data['session']->id;
        $this->data['setSessionCancelledUrl']   = "sessions/set/cancelled/" . $this->data['session']->id;
        $this->data['setSessionConfirmedUrl']   = "sessions/set/confirm/" . $this->data['session']->id;
        $this->data['updateServicesURL']           = "sessions/update/services";

        $this->data['canDelete']           = $user->isOwner();


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

    public function confirmSession($sessionID)
    {
        /** @var Session */
        $session = Session::findOrFail($sessionID);
        $session->confirmSession();

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
        $devices_ids = [];
        if (count($request->device_ids) == 0 || in_array(0, $request->device_ids)) {
            $devices_ids = [];
        } else {
            $devices_ids = $request->device_ids;
        }
        //query
        $this->data['items'] = Session::getSessions(
            $request->branchID,
            null,
            "asc",
            $request->state == 'All' ? [] : [$request->state],
            $request->from,
            $request->to,
            $request->patient,
            $request->doctor,
            $request->opener,
            $request->moneyMan,
            $request->totalBegin,
            $request->totalEnd,
            $request->isCommission,
            false,
            $devices_ids
        );

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
            "roomID"        =>  "required|exists:rooms,id",
            "patientID"     =>  "required|exists:patients,id",
            "doctorID"      =>  "required|exists:dash_users,id",
            "sesDate"       =>  "required|date",
            "start"         =>  "required",
            "end"           =>  "required|after:start",
        ]);
        /** @var DashUser */
        $doctor = DashUser::findOrFail($request->doctorID);

        if (!$doctor->checkUserAvailablity(new Carbon($request->sesDate . ' ' . $request->start), new Carbon($request->sesDate . ' ' . $request->end))) {
            abort(422, "Doctor not available");
        }

        echo Session::createNewSession($request->roomID, $request->patientID, $request->doctorID, $request->sesDate, $request->start, $request->end, $request->comment, $request->servicesArr ? json_decode($request->servicesArr) : [], $request->isCommission ? 1 : 0);
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
        $newDate = new Carbon($request->sessionDate . ' ' . $request->sessionStartTime);
        $oldDate = $session->carbon_date->clone();
        $sendSMS = false;
        if ($newDate->notEqualTo($session->carbon_date)) {
            $sendSMS = true;
        }

        $session->SSHN_PTNT_ID = $request->patientID;
        $session->SSHN_DATE = $request->sessionDate;
        $session->SSHN_STRT_TIME = $request->sessionStartTime;
        $session->SSHN_END_TIME = $request->sessionEndTime;
        $session->SSHN_TEXT = $request->sessionComment;

        if ($session->save() && $sendSMS) {
            SendSMSJob::dispatch($session, SmsHandler::MODE_UPDATE);
            $session->logEvent("Date changed from " . $oldDate->format('l, Y-m-d H:i') . ' to ' . $newDate->format('l, Y-m-d H:i'));
        }

        return redirect("sessions/details/" . $session->id);
    }

    public function delete($sessionID)
    {

        $session = Session::findOrFail($sessionID);
        $session->deleteSession();
        return $this->redirectToHome();
    }

    /////API functions
    public function getSessionsAPI(Request $request)
    {
        $request->validate([
            "branchID"  =>  "required",
            "roomID"    =>  "required",
            "start_date"     =>  "required",
            "end_date"       =>  "required"
        ]);

        $session = Session::getSessions($request->branchID, $request->roomID, 'desc', [Session::STATE_DONE, Session::STATE_NEW, Session::STATE_PENDING_PYMT], $request->start_date, $request->end_date);
        return response()->json($session->map(function ($s) {
            $tmpSession = new stdClass;
            $tmpSession->id = $s->id;
            $tmpSession->title = ($s->SSHN_CONF ? '(c) ' : '') .  $s->doctor->DASH_USNM . ' - ' . $s->patient->PTNT_NAME;
            $tmpSession->start = $s->SSHN_DATE->format("Y-m-d") . 'T' . $s->SSHN_STRT_TIME;
            $tmpSession->end = $s->SSHN_DATE->format("Y-m-d") . 'T' . $s->SSHN_END_TIME;
            // $tmpSession->class_name = $s->class_name;
            $tmpSession->backgroundColor = $s->event_color;
            return $tmpSession;
        }));
    }

    public function getDayNotesAPI(Request $request)
    {
        $request->validate([
            "roomID"    =>  "nullable",
            "start_date"     =>  "required",
            "end_date"       =>  "required"
        ]);

        $notes = DayNote::loadNotes(new Carbon($request->start_date), new Carbon($request->end_date), $request->roomID);
        return response()->json($notes->map(function ($s) {
            $tmpNote = new stdClass;
            $tmpNote->allDay = true;
            $tmpNote->id = 'all-day' . $s->id;
            $tmpNote->title = $s->ADNT_TTLE . ' - ' .  $s->user->DASH_USNM;
            $tmpNote->original_title = $s->ADNT_TTLE;
            $tmpNote->start = $s->ADNT_DATE;
            $tmpNote->roomID = $s->ADNT_ROOM_ID;
            $tmpNote->note = $s->ADNT_NOTE;
            return $tmpNote;
        }));
    }

    public function setNoteAPI(Request $request)
    {
        $request->validate([
            "id"        =>  "nullable|exists:day_notes",
            "roomID"    =>  "nullable|exists:rooms,id",
            "title"     =>  "required",
            "note_date" =>  "required",
            "note"      =>  "nullable"
        ]);

        if (isset($request->id)) {
            /** @var DayNote */
            $note = DayNote::findOrFail($request->id);
            $note->updateInfo($request->title, (new Carbon($request->note_date)), $request->roomID, $request->note);
        } else {
            DayNote::newNote($request->title, (new Carbon($request->note_date)), $request->roomID, $request->note);
        }

        return response()->json();
    }

    public function deleteNoteAPI($noteID)
    {
        /** @var DashUser */
        $user = Auth::user();
        if(!$user->isOwner()) return response()->json([], 403);
        $note = DayNote::findOrFail($noteID);
        $note->delete();
        return response()->json();
    }

    public function getServices(Request $request)
    {

        $request->validate([
            "deviceID" => "required",
            "patientID" => "required"
        ]);

        $device = Device::findOrFail($request->deviceID);

        return json_encode($device->availableServices($request->patientID), JSON_UNESCAPED_UNICODE);
    }

    public function getServicesDuration(Request $request)
    {
        $request->validate([
            "servicesIDs"   =>  "required|array",
            "servicesIDs.*"   =>  "exists:pricelist_items,id",
        ]);

        return response()->json(PriceListItem::calculateTotalDuration($request->servicesIDs));
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
