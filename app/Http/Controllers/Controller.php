<?php

namespace App\Http\Controllers;

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
        $this->data['addPatientFormTitle'] = "Add New Patient";
        $this->data['addPatientFormURL'] = url('patients/insert');
    }

    protected $data;
}
