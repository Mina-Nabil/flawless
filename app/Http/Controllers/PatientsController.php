<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\PatientNote;
use App\Models\PatientPayment;
use App\Models\PriceList;
use App\Models\Room;
use Illuminate\Support\Facades\Session as HttpSession;
use Illuminate\Validation\Rule;

class PatientsController extends Controller
{
    protected $homeURL = "patients/home";

    private function initHomeArr()
    {

        $this->data['title'] = "All Registered Patients";
        $this->data['newCount'] = Patient::getPatientsCountCreatedThisMonth();
        $this->data['patients'] = Patient::with('location')->orderByDesc('id')->cursorPaginate(50);
        $this->data['allCount'] = Patient::count();

        $this->data['patientsTitle'] = "Manage Patients";
        $this->data['patientsSubtitle'] = "Showing All Patients Data";
        $this->data['patientsCols'] = ['Code', 'Full Name', 'Mob#', 'Balance', 'Area', 'Since', 'Promo'];
        $this->data['patientsAtts'] = [
            'id',
            ['attUrl' => ["url" => 'patients/profile', "urlAtt" => 'id', "shownAtt" =>  "PTNT_NAME"]],
            'PTNT_MOBN',
            ['number' => ['att' => 'PTNT_BLNC']],
            ['foreign' => ['att' => 'LOCT_NAME', 'rel' => 'location']],
            ['date' => ['att' => 'created_at', 'format' => 'Y-M-d']],
            'PTNT_PRMO',
        ];
        $this->data['homeURL'] = $this->homeURL;

        //add payment urls
        $this->data['payFormTitle'] = "Add Patient Payment";
        $this->data['payFormSubtite'] = "Add Patient Transaction, will reflect on both patient and cash accounts";
        $this->data['payFormURL'] = url("patients/pay");
    }

    private function initProfileArr($id)
    {
        $this->data['patient'] = Patient::with("location", "sessions", "services", "services.session", "services.session.doctor", "services.pricelistItem", "services.pricelistItem.device", "services.pricelistItem.area", "balanceLogs", "balanceLogs.user", "packageItems", "packageItems.pricelistItem", "packageItems.pricelistItem.area", "packageItems.pricelistItem.device", "followUps", 'followUps.caller')->withCount("sessions")->findOrFail($id);
        $this->data['formURL'] = "patients/update";
        $this->data['addPackagesURL'] = "patients/add/package";
        $this->data['setNoteURL']           = url("patients/setnote");
        $this->data['deleteNoteURL']           = url("patients/notes/delete");
        $this->data['restoreNoteURL']           = url("patients/notes/restore");
        $this->data['title'] = "Patient {$this->data['patient']->PTNT_NAME}'s Profile";
        $this->data['devices']  = Device::all();
        //Services Table
        $this->data['servicesList']    =   $this->data['patient']->services;
        $this->data['pagePageID'] = $this->data['patient']->id;
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

        //Followups Table
        $this->data['followupsList']    =   $this->data['patient']->followUps;
        $this->data['cardTitle'] = false;
        $this->data['followupsCols'] = ['Date', 'State', 'Caller', 'On', 'Comment'];
        $this->data['followupsAtts'] = [
            ['date' => ['att' => 'FLUP_DATE', 'format' => 'd-M-Y']],
            [
                'state'     => [
                    "att"   =>  "FLUP_STTS",
                    "text" => [
                        "New" => "New",
                        "Satified" => "Satified",
                        "7azeen" => "7azeen",
                    ],
                    "classes" => [
                        "New" => "label-info",
                        "7azeen" => "label-danger",
                        "Satified" => "label-success",
                    ],
                ]
            ],
            ['foreign' => ['rel' => 'caller', 'att' => 'DASH_USNM']],
            'FLUP_CALL',
            ['comment' => ['att' => "FLUP_TEXT"]]
        ];

        //Packages Table
        $this->data['packagesList']    =   $this->data['patient']->packageItems;
        $this->data['cardTitle'] = false;
        $this->data['packagesCols'] = ['Service', 'Price', 'Quantity'];
        $this->data['packagesAtts'] = [
            ['foreign' => ['rel' => 'pricelistItem', 'att' => 'item_name']],
            ["number" => ['att' => 'PTPK_PRCE', 'nums' => 2]],
            'PTPK_QNTY',
        ];

        //Pay table
        $this->data['pays'] = PatientPayment::with('dash_user')->where('PTPY_PTNT_ID', '=', $this->data['patient']->id)
            ->orderByDesc('id')->get();
        $this->data['payTitle'] = "Patients Account";
        $this->data['paySubtitle'] = "Check all {$this->data['patient']->PTNT_NAME}'s transactions ";
        $this->data['payCols'] = ['Date', 'User', 'Amount', 'Balance', 'Comment'];
        $this->data['payAtts'] = [
            ['date' => ['att' => 'created_at']],
            ['foreign' =>  ['rel' => 'dash_user', 'att' => 'DASH_USNM']],
            ["number" => ['att' => 'PTPY_PAID', 'nums' => 2]],
            ["number" => ['att' => 'PTPY_BLNC', 'nums' => 2]],
            ["comment" => ['att' => 'PTPY_CMNT']],
        ];

        //Totals Sales
        $this->data['totalGraphs'] =  [];
        $this->data['totalTotals'] =  [];
        $this->data['totalCardTitle'] =  "Total KGs";
        $this->data['totalTitle'] =  "Monthly Report";
        $this->data['totalSubtitle'] =  "Check total KGs bought each month";

        //Items Bought
        $this->data['boughtList'] = $this->data['patient']->servicesTaken();
        $this->data['boughtCols'] = ['Model', 'Price', 'KGs'];
        $this->data['boughtAtts'] = [
            'PROD_NAME',
            'ORIT_PRCE',
            'ORIT_KGS'
        ];

        //payment form
        $this->data['payFormTitle'] = "Add New Patient Payment for " . $this->data['patient']->PTNT_NAME;
        $this->data['payFormURL'] = url('patients/pay');

        //balance form
        $this->data['addBalanceTitle'] = "Add Direct Balance for " . $this->data['patient']->PTNT_NAME;
        $this->data['addBalanceURL'] = url('patients/addbalance');
    }

    public function home()
    {
        $this->initHomeArr();
        return view("patients.main", $this->data);
    }

    public function add()
    {
        $this->initAddArr();
        return view("patients.add", $this->data);
    }

    public function profile($id)
    {
        $this->initProfileArr($id);
        return view("patients.profile", $this->data);
    }

    public function pay(Request $request)
    {

        $request->validate([
            "amount"        => "required|numeric",
            "patientID"     => "required|exists:patients,id",
            "branchID"     => "required|exists:branches,id",
        ]);

        /** @var Patient */
        $patient = Patient::findOrFail($request->patientID);
        $isVisa = (isset($request->isVisa) &&  $request->isVisa == "on") ? true : false;
        $patient->pay($request->branchID, $request->amount, $request->comment, true, $isVisa);
        if ($request->goToHome) {
            return redirect($this->homeURL);
        } else {
            return redirect("patients/profile/" . $patient->id);
        }
    }

    public function addBalance(Request $request)
    {
        $request->validate([
            "title"         => "required",
            "amount"        => "required|numeric",
            "patientID"     => "required|exists:patients,id",
        ]);

        $patient = Patient::findOrFail($request->patientID);
        $patient->addBalance($request->title, $request->amount, $request->comment);
        return redirect("patients/profile/" . $patient->id);
    }

    public function addPackage(Request $request)
    {
        $request->validate([
            "id" => "required",
            "branchID"     => "required|exists:branches,id",
        ]);

        /** @var Patient */
        $patient = Patient::findOrFail($request->id);
        $isVisa = (isset($request->cashRadio) &&  $request->cashRadio == "visa") ? true : false;
        $branch_ID = $request->branchID ?? HttpSession::get('branch');
        $this->data['rooms']    =   Room::byBranch($branch_ID)->get();
        if (isset($request->service))
            foreach ($request->service as $key => $pricelistID) {
                $patient->submitNewPackage($request->branchID, $pricelistID, $request->unit[$key], $request->price[$key] / $request->unit[$key], $isVisa);;
            }
        return redirect("patients/profile/" . $patient->id);
    }

    public function insert(Request $request)
    {
        $request->validate([
            "name"              => "required",
            "mobn"               => "required|numeric|unique:patients,PTNT_MOBN",
            "channelID"         => "required|exists:channels,id",
            "locationID"       => "required|exists:locations,id",
        ]);

        $patient = new Patient();
        $patient->PTNT_NAME = $request->name;
        $patient->PTNT_ADRS = $request->adrs;
        $patient->PTNT_MOBN = $request->mobn;
        $patient->PTNT_BLNC = $request->balance ?? 0;
        $patient->PTNT_PRLS_ID = $request->listID ?? (PriceList::getDefaultList()->id ?? NULL);
        $patient->PTNT_CHNL_ID = $request->channelID;
        $patient->PTNT_LOCT_ID = $request->locationID;
        $patient->PTNT_NOTE = $request->note;
        $patient->PTNT_PRMO = $request->promo;
        $patient->save();

        return $patient->id;
    }

    public function update(Request $request)
    {
        $request->validate([
            "id"          => "required",
        ]);
        $patient = Patient::findOrFail($request->id);
        $request->validate([
            "name"  => ["required", Rule::unique('patients', "PTNT_NAME")->ignore($patient->PTNT_NAME, "PTNT_NAME"),],
            "mobn"  => ["required", "numeric",  Rule::unique('patients', "PTNT_MOBN")->ignore($patient->PTNT_MOBN, "PTNT_MOBN")],
            "channelID"         => "required|exists:channels,id",
            "locationID"       => "required|exists:locations,id",
        ]);

        $patient->PTNT_NAME = $request->name;
        $patient->PTNT_ADRS = $request->adrs;
        $patient->PTNT_MOBN = $request->mobn;
        $patient->PTNT_BLNC = $request->balance ?? 0;
        $patient->PTNT_PRLS_ID = $request->listID ?? (PriceList::getDefaultList()->id ?? NULL);
        $patient->PTNT_CHNL_ID = $request->channelID;
        $patient->PTNT_LOCT_ID = $request->locationID;
        $patient->PTNT_NOTE = $request->note;
        $patient->save();

        return redirect("patients/profile/" . $patient->id);
    }

    public function setNote(Request $request)
    {
        $request->validate([
            "id"    =>  "required|exists:patients",
            "note"  =>  "required"
        ]);
        /** @var Patient */
        $patient = Patient::findOrFail($request->id);
        $patient->addNote($request->note);
        return back();
    }

    public function deleteNote($id)
    {
        /** @var PatientNote */
        $patientNote = PatientNote::findOrFail($id);
        $patientNote->deleteNote();
        return back();
    }

    public function restoreNote($id)
    {
        /** @var PatientNote */
        $patientNote = PatientNote::withTrashed()->findOrFail($id);
        $patientNote->restore();
        return back();
    }


    //////?API function

    public function getJSONPatients(Request $request)
    {
        $search = $request->input("search");
        return json_encode(Patient::orderByDesc('id')->when($search, function ($q, $v) {
            $q->searchBy($v);
        })->get(), JSON_UNESCAPED_UNICODE);
    }
}
