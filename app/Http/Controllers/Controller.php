<?php

namespace App\Http\Controllers;

use App\Models\DashUser;
use App\Models\PriceList;
use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function __construct()
    {
        if (!request()->is('login')) {
            $this->middleware('auth');
            $this->middleware("\App\Http\Middleware\CheckType");
            $this->setMainDataItems();
        }
    }

    private function setMainDataItems()
    {
        $this->data['addPatientFormTitle']  = "Add New Patient";
        $this->data['addPatientFormURL']    = url('patients/insert');
        $this->data['getPatientsURL']       = url('patients/get/json');
        $this->data['addSessionFormURL']    = url('sessions/insert');
        $this->data['addAttendanceURL']    = url('attendance/insert');

        $this->data['allPricelists']        =   PriceList::all();
        $this->data['doctors']              =   DashUser::where("DASH_TYPE_ID", 2)->get();
    }

    protected $data;
}
