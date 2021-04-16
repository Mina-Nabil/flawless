<?php

namespace App\Http\Controllers;

use App\Models\Visa;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;

class VisaController extends Controller
{
    protected $data;

    private function setDataArr()
    {
        //Trans table
        $this->data['todayTrans'] = Visa::with("dash_user")->whereDate('created_at', Carbon::today())->orderByDesc('id')->get();
        $this->data['todayTitle'] = "Today's Transactions";
        $this->data['todaySubtitle'] = "Check all transactions from the starting of today " . Carbon::today()->format('d/M/Y');
        $this->data['title'] = "Visa Account Page";
        $this->data['todayCols'] = ['Date', 'User', 'Title', 'In', 'Out', 'Balance', 'Comment'];
        $this->data['todayAtts'] = [
            ['date' => ['att' => 'created_at']],
            ['foreign' => ['rel' => 'dash_user', 'att' => 'DASH_USNM']],
            'VISA_DESC',
            ["number" => ['att' => 'VISA_IN', 'nums' => 2]],
            ["number" => ['att' => 'VISA_OUT', 'nums' => 2]],
            ["number" => ['att' => 'VISA_BLNC', 'nums' => 2]],
            ["comment" => ['att' => 'VISA_CMNT']],
        ];
        //Trans table
        $this->data['trans'] = Visa::with("dash_user")->orderByDesc('id')->limit(300)->get();
        $this->data['transTitle'] = "More Transactions";
        $this->data['transSubtitle'] = "Check Latest 300 visa transaction";
        $this->data['transCols'] = ['Date', 'User', 'Title', 'In', 'Out', 'Balance', 'Comment'];
        $this->data['transAtts'] = [
            ['date' => ['att' => 'created_at']],
            ['foreign' => ['rel' => 'dash_user', 'att' => 'DASH_USNM']],
            'VISA_DESC',
            ["number" => ['att' => 'VISA_IN', 'nums' => 2]],
            ["number" => ['att' => 'VISA_OUT', 'nums' => 2]],
            ["number" => ['att' => 'VISA_BLNC', 'nums' => 2]],
            ["comment" => ['att' => 'VISA_CMNT']],
        ];

        $this->data['formURL'] = url('visa/insert');
        $this->data['formTitle'] = "Add Visa Transaction";
        $this->data['balance'] = Visa::currentBalance();
        $this->data['paidToday'] = Visa::paidToday();
        $this->data['collectedToday'] = Visa::collectedToday();
        $this->data['startingBalance'] = Visa::yesterdayBalance();
    }

    public function home()
    {
        $this->setDataArr();
        return view('accounts.visa', $this->data);
    }

    public function insert(Request $request)
    {
        $request->validate([
            "title"              => "required",
            "in"              => "required|numeric",
            "out"              => "required|numeric",
        ]);
        Visa::entry($request->title, $request->in, $request->out, $request->comment);
        return redirect("visa/home");
    }

    public function query()
    {
        $this->data['title']        = "Visa Report";
        $this->data['formTitle']    = "Prepare Visa Report";
        $this->data['formSubtitle']      = 'View Visa Transactions from Start Date till End Date';

        return view('accounts.query', $this->data);
    }

    public function loadQuery(Request $request)
    {
        $request->validate([
            "from"  =>  "required",
            "to"    =>  "required"
        ]);

        $startDate  = (new DateTime($request->from))->format('Y-m-d 00:00:00');
        $endDate    = (new DateTime($request->to))->format('Y-m-d 23:59:59');

        //query
        $this->data['items'] = Visa::with("dash_user")->whereBetween('created_at', [$startDate, $endDate])->orderByDesc('id')->get();
        $totalOut = $this->data['items']->sum('VISA_OUT');

        $this->data['cols'] = ['Date', 'User', 'Title', 'In', 'Out', 'Balance', 'Comment'];

        $this->data['atts'] = [
            ['date' => ['att' => 'created_at']],
            ['foreign' => ['rel' => 'dash_user', 'att' => 'DASH_USNM']],
            'VISA_DESC',
            ["number" => ['att' => 'VISA_IN', 'nums' => 2]],
            ["number" => ['att' => 'VISA_OUT', 'nums' => 2]],
            ["number" => ['att' => 'VISA_BLNC', 'nums' => 2]],
            ["comment" => ['att' => 'VISA_CMNT']],
        ];

        //table info
        $this->data['title'] = "FLAWLESS Dashboard";
        $this->data['tableTitle'] = "Visa Report";
        $this->data['tableSubtitle'] = "Showing Visa Transactions from " . (new DateTime($request->from))->format('d-M-Y') . " to " . (new DateTime($request->to))->format('d-M-Y') . ' -- Total Spent: ' . $totalOut;

        return view("layouts.table", $this->data);
    }
}
