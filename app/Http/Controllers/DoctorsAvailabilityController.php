<?php

namespace App\Http\Controllers;

use App\Models\DoctorAvailability;
use App\Models\Patient;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DoctorsAvailabilityController extends Controller
{
    function availabilities()
    {
        $this->data = self::getDoctorAvailabilitiesDataArray();
        return view("settings.availabilities", $this->data);
    }

    function availability($id)
    {
        /** @var DoctorAvailability */
        $this->data = self::getDoctorAvailabilitiesDataArray($id);

        return view("settings.availabilities", $this->data);
    }

    function addDoctorAvailability(Request $request)
    {
        /** @var DoctorAvailability */
        $request->validate([
            "branch_id" =>  "required|exists:branches,id",
            "doctor_id" =>  "required|exists:dash_users,id",
            "day"       =>  "required|in:" . implode(',', array_keys(DoctorAvailability::DAYS_ARR)),
            "shift"     =>  "required|in:" . implode(',', DoctorAvailability::SHIFTS_ARR),
        ]);
        $availability = DoctorAvailability::newDoctorAvailability($request->branch_id, $request->doctor_id, $request->day, $request->shift, $request->note);
        return redirect()->action([self::class, "availabilities"]);
    }

    function getDoctorAvailability(Request $request)
    {
        $request->validate([
            "room_id"       =>  "required|exists:rooms,id",
            "patient_id"    =>  "required|exists:patients,id",
            "date"          =>  "required",
            "start_time"    =>  "required",
            "end_time"      =>  "required",
        ]);

        $midShifts = (new Carbon($request->date))->setHour(16);
        $start_time = new Carbon($request->date . ' ' . $request->start_time);
        $shift = $start_time->isBefore($midShifts) ? DoctorAvailability::SHIFT_1 : DoctorAvailability::SHIFT_2;

        $room = Room::findOrFail($request->room_id);

        $doctors = DoctorAvailability::getAvailableDoctors($midShifts, $shift, null, $room->ROOM_BRCH_ID);

        if ($request->patient_id != null) {
            $patient = Patient::with('doctors')->findOrFail($request->patient_id);
        
            foreach ($doctors as $doc) {
                if ($patient->doctors->contains('id', $doc['doctor']->id)) {
                    $doc['doctor']->old_doc = true;
                } else {
                    $doc['doctor']->old_doc = false;
                }
            }
        }

        return response()->json($doctors);
    }

    function updateDoctorAvailability($id, Request $request)
    {
        /** @var DoctorAvailability */
        $availability = DoctorAvailability::findOrFail($id);
        $request->validate([
            "branch_id" =>  "required|exists:branches,id",
            "doctor_id" =>  "required|exists:dash_users,id",
            "day"       =>  "required|in:" . implode(',', array_keys(DoctorAvailability::DAYS_ARR)),
            "shift"     =>  "required|in:" . implode(',', DoctorAvailability::SHIFTS_ARR),
        ]);
        $availability->updateInfo($request->branch_id, $request->doctor_id, $request->day, $request->shift, $request->note);
        return redirect()->action([self::class, "availabilities"]);
    }

    function deleteAvailability($id)
    {
        /** @var DoctorAvailability */
        $availability = DoctorAvailability::findOrFail($id);
        $availability->delete();
        return redirect()->action([self::class, "availabilities"]);
    }

    private function getDoctorAvailabilitiesDataArray($availabilityID = null)
    {
        $this->data['items'] = DoctorAvailability::all();
        if ($availabilityID) {
            $this->data['availability'] = DoctorAvailability::findOrFail($availabilityID);
        }
        $this->data['title']        =   "Doctors Default Availability";
        $this->data['subTitle'] = "Manage Flawless doctors default schedule";
        $this->data['formTitle'] =  "Add/Edit Availability Records";
        $this->data['days'] = DoctorAvailability::DAYS_ARR;
        $this->data['shifts'] = DoctorAvailability::SHIFTS_ARR;
        $this->data['cols'] = ['Day', 'Shift', 'Branch', 'Doctor', 'Note', 'Edit', 'Delete'];
        $this->data['atts'] = [
            'DVAC_DAY_OF_WEEK',
            'DCAV_SHFT',
            ['foreign' => ['rel' => 'branch', 'att' => 'BRCH_NAME']],
            ['foreign' => ['rel' => 'doctor', 'att' => 'DASH_USNM']],
            ['comment' => ['att' => 'DCAV_NOTE']],
            ['edit' => ['url' => 'availabilities/', 'att' => 'id']],
            ['del' => ['url' => 'availabilities/delete/', 'att' => 'id']]
        ];

        $this->data['formURL']   =  ($availabilityID) ? url('availabilities/update') . '/' . $this->data['availability']->id : url('availabilities');
        $this->data['homeURL'] = 'availabilities';
        $this->data['isCancel'] = $availabilityID;
        return $this->data;
    }
}
