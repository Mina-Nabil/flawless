<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Device;
use App\Models\Patient;
use App\Models\PatientMessage;
use App\Models\Session;
use Illuminate\Http\Request;

class PatientMessagesController extends Controller
{
    /**
     * Display the patient messages management page
     *
     * @param int|null $id Optional ID to pre-load for editing
     * @return \Illuminate\View\View
     */
    function index($id = null)
    {
        $this->data['title'] = "Manage Patient Messages";
        $this->data['patientMessages'] = PatientMessage::with(['device', 'area'])->get();
        $this->data['devices'] = Device::all();
        $this->data['areas'] = Area::all();
        $this->data['addPatientMessageURL'] = url('patient-messages/add');
        $this->data['editPatientMessageURL'] = url('patient-messages/edit');
        $this->data['delPatientMessageURL'] = url('patient-messages/delete');

        // If ID is provided, pre-load the message for editing
        if ($id) {
            $this->data['selectedMessage'] = PatientMessage::with(['device', 'area'])->findOrFail($id);
        } else {
            $this->data['selectedMessage'] = null;
        }

        // Get a sample session for preview (or create a mock session with a sample patient)
        $samplePatient = Patient::first();
        $sampleSession = null;
        if ($samplePatient) {
            // Create a mock session object for preview purposes
            $sampleSession = new Session();
            $sampleSession->setRelation('patient', $samplePatient);
        } else {
            // If no patients exist, create a mock patient for preview
            $mockPatient = new Patient();
            $mockPatient->PTNT_NAME = 'John Doe';
            $sampleSession = new Session();
            $sampleSession->setRelation('patient', $mockPatient);
        }
        $this->data['sampleSession'] = $sampleSession;

        return view("settings.patient_messages", $this->data);
    }
    /**
     * Create a new patient message
     *
     * @param Request $request
     * @return int
     */
    function addPatientMessage(Request $request)
    {
        $request->validate([
            "device_id" => "required|exists:devices,id",
            "area_id" => "nullable|exists:areas,id",
            "message" => "required|string",
        ]);

        $patientMessage = new PatientMessage();
        $patientMessage->PTMS_DVIC_ID = $request->device_id;
        $patientMessage->PTMS_AREA_ID = $request->area_id;
        $patientMessage->PTMS_MSSG = $request->message;

        $patientMessage->save();
        return $patientMessage->id;
    }

    /**
     * Edit an existing patient message
     *
     * @param Request $request
     * @return int
     */
    function editPatientMessage(Request $request)
    {
        $request->validate([
            "id" => "required",
        ]);

        $patientMessage = PatientMessage::findOrFail($request->id);

        $request->validate([
            "id" => "required",
            "device_id" => "required|exists:devices,id",
            "area_id" => "nullable|exists:areas,id",
            "message" => "required|string",
        ]);

        $patientMessage->PTMS_DVIC_ID = $request->device_id;
        $patientMessage->PTMS_AREA_ID = $request->area_id;
        $patientMessage->PTMS_MSSG = $request->message;

        $patientMessage->save();
        return $patientMessage->id;
    }

    /**
     * Delete a patient message
     *
     * @param Request $request
     * @return string
     */
    function deletePatientMessage(Request $request)
    {
        $request->validate([
            "id" => "required",
        ]);

        $patientMessage = PatientMessage::findOrFail($request->id);
        $patientMessage->delete();
        return "1";
    }
}
