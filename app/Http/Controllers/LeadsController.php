<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LeadsController extends Controller
{
    protected $homeURL = "leads";

    private function initHomeArr()
    {

        $this->data['title'] = "Leads Management Page";
        $this->data['thisMonth'] = Lead::getCountCreatedThisMonth();
        $this->data['leads'] = Lead::byUser()->orderByDesc('id')->get();

        $this->data['newCount'] = $this->data['leads']->where('LEAD_STTS', Lead::STTS_NEW)->count();
        $this->data['interestedCount'] = $this->data['leads']->where('LEAD_STTS', Lead::STTS_INTERESTED)->count();
        $this->data['patientsCount'] = $this->data['leads']->where('LEAD_STTS', Lead::STTS_PATIENT)->count();
        $this->data['allCount'] = $this->data['leads']->count();
        $this->data['followupsCount'] = FollowUp::getLeadFollowupsCount();

        $this->data['leadsTitle'] = "Manage Leads";
        $this->data['leadsSubtitle'] = "Showing All leads Data";
        $this->data['leadsCols'] = ['Promo', 'Name', 'Mob#', 'Status', 'Since', 'Patient Profile', "Actions"];
        $this->data['leadsAtts'] = [
            'LEAD_PRMO',
            'LEAD_NAME',
            'LEAD_MOBN',
            [
                'leadState'     => [
                    "att"   =>  "LEAD_STTS",
                    "text" => [
                        Lead::STTS_NEW => "New",
                        Lead::STTS_INTERESTED => "Interested",
                        Lead::STTS_NOT_INTERESTED => "Not interested",
                        Lead::STTS_NO_ANSWER => "No Answer",
                        Lead::STTS_PATIENT => "Patient",
                    ],
                    "classes" => [
                        Lead::STTS_NEW => "label-info",
                        Lead::STTS_INTERESTED => "label-info",
                        Lead::STTS_NOT_INTERESTED => "label-danger",
                        Lead::STTS_NO_ANSWER => "label-dark",
                        Lead::STTS_PATIENT => "label-success",
                    ],
                ]
            ],
            ['date' => ['att' => 'created_at', 'format' => 'Y-M-d']],
            ['foreign' => ['att' => 'PTNT_NAME', 'rel' => 'patient']],
            ['leadsActions' => ["N/A"]],
        ];
        $this->data['homeURL'] = $this->homeURL;
        $this->data['addLeadsFormTitle'] = 'New Lead';
        $this->data['addLeadsFormUrl'] = 'leads/insert';
        $this->data['updateLeadsFormUrl'] = 'leads/update';
        $this->data['setstatusFormUrl'] = 'leads/setstatus';
        $this->data['addLeadFollowup'] = 'leads/addfollowup';
        $this->data['addAsPatient'] = 'leads/addaspatient';
        $this->data['download'] = 'leads/download/export';
        $this->data['import'] = 'leads/import/template';
        $this->data['template'] = 'leads/download/template';
    }

    public function home()
    {
        $this->initHomeArr();
        return view("patients.leads", $this->data);
    }

    public function downloadLeads()
    {
        return Lead::exportLeads();
    }

    public function downloadTemplate()
    {
        return Lead::downloadTemplate();
    }

    public function importLeads(Request $request)
    {
        Log::info($request);
        $request->validate([
            "import_file" => "required|file",
        ]);

        Lead::importLeads($request->import_file);
        return redirect()->action([self::class, 'home']);
    }

    public function insert(Request $request)
    {
        $request->validate([
            "name"              => "required",
            "mobn"               => "required|numeric|unique:leads,LEAD_MOBN",
        ]);

        /** @var DashUser */
        $user = Auth::user();

        Lead::newLead($request->user_id && $user->isAdmin() ? $request->user_id : $user->id, $request->name, $request->mobn, $request->promo, $request->address, $request->note);

        return redirect()->action([self::class, 'home']);
    }

    public function update(Request $request)
    {
        $request->validate([
            "id"          => "required",
        ]);
        /** @var Lead */
        Lead::findOrFail($request->id);
        $request->validate([
            "name"              => "required",
            "mobn"               => "required|numeric",
        ]);

        return redirect()->action([self::class, 'home']);
    }

    public function setStatus(Request $request)
    {
        $request->validate([
            "leadID"          => "required",
        ]);
        /** @var Lead */
        $lead = Lead::findOrFail($request->leadID);
        $request->validate([
            "status"    => "required|in:" . implode(",", Lead::STATUSES),
        ]);
        $lead->setStatus($request->status);

        return 1;
    }

    public function addLeadFollowup(Request $request)
    {
        $request->validate([
            "leadID"    => "required",
        ]);
        /** @var Lead */
        $lead = Lead::findOrFail($request->leadID);
        $request->validate([
            "callDate"      => "required|date",
        ]);
        $lead->addFollowup(new Carbon($request->callDate), $request->leadNote);

        return ($lead->id) ? 1 : "NO";
    }

    public function addToPatients($leadID)
    {
        /** @var Lead */
        $lead = Lead::findOrFail($leadID);
        $newPatient = $lead->createPatient();
        return redirect()->action([PatientsController::class, 'profile'], [$newPatient->id]);
    }

    public function deleteLead($leadID)
    {
        /** @var Lead */
        $lead = Lead::findOrFail($leadID);
        $lead->deleteLead();
        return redirect()->action([self::class, 'home']);
    }
}
