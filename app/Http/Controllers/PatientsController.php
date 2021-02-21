<?php

namespace App\Http\Controllers;

use App\Models\Area;
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
        $this->data['patients'] = Patient::orderByDesc('id')->get();
        $this->data['allCount'] = $this->data['patients']->count();

        $this->data['patientsTitle'] = "Manage Patients";
        $this->data['patientsSubtitle'] = "Showing All Patients Data";
        $this->data['patientsCols'] = ['Code', 'Full Name', 'Mob#', 'Balance', 'Address', 'Since'];
        $this->data['patientsAtts'] = [
            'id',
            ['attUrl' => ["url" => 'patients/profile', "urlAtt" => 'id', "shownAtt" =>  "PTNT_NAME"]],
            'PTNT_MOBN',
            ['number' => ['att' => 'PTNT_BLNC']],
            ['comment' => ['att' => 'PTNT_ADRS']],
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
        $this->data['patient'] = Patient::findOrFail($id);
        $this->data['formURL'] = "patients/update";

        //Orders Array
        $this->data['orderList']    =  [];   #Session::getOrdersByPatient($id);
        $this->data['cardTitle'] = false;
        $this->data['title'] = $this->data['patient']->PTNT_NAME . "'s Patient Profile";
        $this->data['ordersCols'] = ['id', 'Status', 'Payment',  'Items', 'Ordered On', 'Total'];
        $this->data['orderAtts'] = [
            ['attUrl' => ['url' => "orders/details", "shownAtt" => 'id', "urlAtt" => 'id']],
            [
                'stateQuery' => [
                    "classes" => [
                        "1" => "label-info",
                        "2" => "label-warning",
                        "3" =>  "label-dark bg-dark",
                        "4" =>  "label-success",
                        "5" =>  "label-danger",
                        "6" =>  "label-primary",
                    ],
                    "att"           =>  "ORDR_STTS_ID",
                    'foreignAtt'    => "STTS_NAME",
                    'url'           => "orders/details/",
                    'urlAtt'        =>  'id'
                ]
            ],
            'PYOP_NAME',
            'itemsCount',
            'ORDR_OPEN_DATE',
            'ORDR_TOTL'
        ];

        //Pay table
        $this->data['pays'] = PatientPayment::where('PTPY_PTNT_ID', '=', $this->data['patient']->id)
            ->orderByDesc('id')->get();
        $this->data['payTitle'] = "Patients Account";
        $this->data['paySubtitle'] = "Check all {$this->data['patient']->PTNT_NAME}'s transactions ";
        $this->data['payCols'] = ['Date', 'Paid By', 'Amount', 'Balance', 'Comment'];
        $this->data['payAtts'] = [
            ['date' => ['att' => 'created_at']],
            ['foreign' => ["rel" => 'dash_user', "att" => 'DASH_USNM']],
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
        $this->data['payFormTitle'] = "Add New Patient Payment for " .  $this->data['patient']->PTNT_NAME;
        $this->data['payFormURL'] = url('patients/pay');
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
            "amount"         => "required|numeric",
            "patientID"             => "required|exists:patients,id",
        ]);

        $patient = Patient::findOrFail($request->patientID);
        $patient->pay($request->amount, $request->comment);
        if($request->goToHome){
            return redirect($this->homeURL);
        } else {
            return redirect("patients/profile/" . $patient->id);
        }
    }

    public function insert(Request $request)
    {
        $request->validate([
            "name"              => "required",
            "mobn"               => "required|numeric|unique:patients,PTNT_MOBN",
        ]);

        $patient = new Patient();
        $patient->PTNT_NAME = $request->name;
        $patient->PTNT_ADRS = $request->adrs;
        $patient->PTNT_MOBN = $request->mobn;
        $patient->PTNT_BLNC = $request->balance ?? 0;
        $patient->PTNT_PRLS_ID = PriceList::getDefaultList()->id ?? NULL;
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
        ]);

        $patient->PTNT_NAME = $request->name;
        $patient->PTNT_ADRS = $request->adrs;
        $patient->PTNT_MOBN = $request->mobn;
        $patient->PTNT_BLNC = $request->balance ?? 0;

        $patient->save();

        return redirect("patients/profile/" . $patient->id);
    }
}
