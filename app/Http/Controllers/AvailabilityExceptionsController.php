<?php

namespace App\Http\Controllers;

use App\Models\AvailabilityException;
use App\Models\DoctorAvailability;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AvailabilityExceptionsController extends Controller
{
    function exceptions()
    {
        $this->data = self::getExceptionsDataArray();

        return view("attendance.exceptions", $this->data);
    }

    function addException(Request $request)
    {
        /** @var AvailabilityException */
        $request->validate([
            "branch_id"     =>  "required|exists:branches,id",
            "doctor_id"     =>  "required|exists:dash_users,id",
            "date"          =>  "required|date",
            "shift"         =>  "required|in:" . implode(',', DoctorAvailability::SHIFTS_ARR),
        ]);
        $availability = AvailabilityException::newException($request->doctor_id, $request->branch_id, new Carbon($request->date), $request->shift, $request->note, !is_null($request->coming));
        return redirect()->action([self::class, "exceptions"]);
    }



    function deleteException($id)
    {
        /** @var AvailabilityException */
        $availability = AvailabilityException::findOrFail($id);
        $availability->delete();
        return redirect()->action([self::class, "exceptions"]);
    }

    private function getExceptionsDataArray()
    {
        $this->data['items'] = AvailabilityException::all();
        $this->data['title']        =   "Doctors Attendance Exceptions";
        $this->data['subTitle'] = "Add/Delete Upcoming Exceptions";
        $this->data['formTitle'] =  "Add New Attendance Exception";
        $this->data['shifts'] = DoctorAvailability::SHIFTS_ARR;
        $this->data['cols'] = ['Date', 'Shift', 'Branch', 'Doctor', 'In/Out', 'Comment', 'Delete'];
        $this->data['atts'] = [
            'EXPT_DATE',
            'EXPT_SHFT',
            ['foreign' => ['rel' => 'branch', 'att' => 'BRCH_NAME']],
            ['foreign' => ['rel' => 'doctor', 'att' => 'DASH_USNM']],
            [
                'state' => [
                    "att"   =>  "EXPT_AVAL",
                    "text" => [
                        "1" => "Yes",
                        "0" => "No",
                    ],
                    "classes" => [
                        "1" => "label-success",
                        "0" => "label-danger",
                    ],
                ]
            ],
            ['comment' => ['att' => 'EXPT_DESC']],
            ['del' => ['url' => 'exceptions/delete/', 'att' => 'id']]
        ];

        $this->data['formURL']   =  url('exceptions');
        $this->data['homeURL'] = 'exceptions';

        return $this->data;
    }
}
