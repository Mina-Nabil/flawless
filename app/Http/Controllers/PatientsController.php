<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Device;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\PatientPayment;
use App\Models\PriceList;
use App\Models\Session;
use App\Rules\triplename;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PatientsController extends Controller
{
    protected $homeURL = "patients/home";

    private function initHomeArr()
    {

        $this->data['title'] = "All Registered Patients";
        $this->data['newCount'] = Patient::getPatientsCountCreatedThisMonth();
        $this->data['patients'] = Patient::with('location')->orderByDesc('id')->get();
        $this->data['allCount'] = $this->data['patients']->count();

        $this->data['patientsTitle'] = "Manage Patients";
        $this->data['patientsSubtitle'] = "Showing All Patients Data";
        $this->data['patientsCols'] = ['Code', 'Full Name', 'Mob#', 'Balance', 'Area', 'Since'];
        $this->data['patientsAtts'] = [
            'id',
            ['attUrl' => ["url" => 'patients/profile', "urlAtt" => 'id', "shownAtt" =>  "PTNT_NAME"]],
            'PTNT_MOBN',
            ['number' => ['att' => 'PTNT_BLNC']],
            ['comment' => ['att' => 'PTNT_ADRS']],
            ['foreign' => ['att' => 'LOCT_NAME', 'rel' => 'location']],
            ['date' => ['att' => 'created_at', 'format' => 'Y-M-d']],
        ];
        $this->data['homeURL'] = $this->homeURL;

        //add payment urls
        $this->data['payFormTitle'] = "Add Patient Payment";
        $this->data['payFormSubtite'] = "Add Patient Transaction, will reflect on both patient and cash accounts";
        $this->data['payFormURL'] = url("patients/pay");
    }

    private function initProfileArr($id)
    {
        $this->data['patient'] = Patient::with("location", "sessions", "services", "services.session", "services.session.doctor", "services.pricelistItem", "services.pricelistItem.device", "services.pricelistItem.area", "balanceLogs", "balanceLogs.user", "packageItems", "packageItems.pricelistItem", "packageItems.pricelistItem.area", "packageItems.pricelistItem.device")->withCount("sessions")->findOrFail($id);
        $this->data['formURL'] = "patients/update";
        $this->data['addPackagesURL'] = "patients/add/package";
        $this->data['title'] = "Patient {$this->data['patient']->PTNT_NAME}'s Profile";
        $this->data['devices']  = Device::all();
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

        $this->data['getServicesAPI']           = "sessions/api/get/services";

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
        $this->data['pays'] = PatientPayment::where('PTPY_PTNT_ID', '=', $this->data['patient']->id)
            ->orderByDesc('id')->get();
        $this->data['payTitle'] = "Patients Account";
        $this->data['paySubtitle'] = "Check all {$this->data['patient']->PTNT_NAME}'s transactions ";
        $this->data['payCols'] = ['Date', 'Amount', 'Balance', 'Comment'];
        $this->data['payAtts'] = [
            ['date' => ['att' => 'created_at']],
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
        ]);

        $patient = Patient::findOrFail($request->patientID);
        $isVisa = (isset($request->isVisa) &&  $request->isVisa == "on") ? true : false;
        $patient->pay($request->amount, $request->comment, true, $isVisa);
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
            "id" => "required"
        ]);
        $patient = Patient::findOrFail($request->id);
        $isVisa = (isset($request->cashRadio) &&  $request->cashRadio == "visa") ? true : false;
        if (isset($request->service))
            foreach ($request->service as $key => $pricelistID) {
                $patient->addPackage($pricelistID, $request->unit[$key], $request->price[$key] / $request->unit[$key]);
                $patient->pay($request->price[$key], "Payment added from adding package", true, $isVisa, false);
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

        $patient->save();

        return redirect("patients/profile/" . $patient->id);
    }


    //////?API function

    public function getJSONPatients()
    {
        return json_encode(Patient::orderByDesc('id')->get(), JSON_UNESCAPED_UNICODE);
    }
}
