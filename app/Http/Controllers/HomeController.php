<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\DashUser;
use App\Models\Patient;
use App\Models\PushMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public function login()
    {

        $data['username'] = '';
        $data['first'] = true;
        return view('auth/login', $data);
    }

    public function authenticate(Request $request)
    {
        if (Auth::check()) return redirect('/home');

        $userName = $request->input('userName');
        $passWord = $request->input('passWord');

        $data['first'] = true;

        if (isset($userName)) {
            if (Auth::attempt(array('DASH_USNM' => $userName, 'password' => $passWord, 'DASH_ACTV' => 1), true)) {
                //logging in
                if (Auth::user()->isDoctor() == 2) //if doctor
                    Attendance::createAttendance(Auth::user()->id, date('Y-m-d'));
                return redirect('/home');
            } else {
                $data['first'] = false;
                $data['username'] = $userName;
                return view('auth/login', $data);
            }
        } else {
            redirect("login");
        }
    }

    public function search(Request $request)
    {

        $request->validate([
            "searchVal" => "required"
        ]);

        $this->data['title'] = "FLAWLESS Dashboard";
        $this->data['items'] = Patient::orderByDesc('id')->whereRaw("PTNT_NAME LIKE '%{$request->searchVal}%' OR PTNT_MOBN LIKE '%{$request->searchVal}%' ")->get();
        $this->data['allCount'] = $this->data['patients']->count();

        $this->data['tableTitle'] = "Search Results";
        $this->data['tableSubtitle'] = "Showing Results for '{$request->searchVal}'";
        $this->data['cols'] = ['Code', 'Full Name', 'Mob#', 'Balance', 'Address', 'Since'];
        $this->data['atts'] = [
            'id',
            ['attUrl' => ["url" => 'patients/profile', "urlAtt" => 'id', "shownAtt" =>  "PTNT_NAME"]],
            'PTNT_MOBN',
            ['number' => ['att' => 'PTNT_BLNC']],
            ['comment' => ['att' => 'PTNT_ADRS']],
            ['date' => ['att' => 'created_at', 'format' => 'Y-M-d']],
        ];
        return view('layouts.table', $this->data);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('login');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            "userID"    =>  "required",
            "from"      =>  "required",
            "message"   =>  "required"
        ]);
        $userFrom = DashUser::findOrFail($request->from);
        event(new PushMessage($request->userID, $userFrom->DASH_USNM, $request->message));
        echo 1;
    }
}
