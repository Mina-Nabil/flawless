<?php

namespace App\Http\Controllers;

use App\Models\Visa;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session as HttpSession;

class VisaController extends Controller
{
    protected $data;

    private function setDataArr()
    {
        //Trans table
        $branch_ID = HttpSession::get('branch');
        $this->data['todayTrans'] = Visa::today($branch_ID)->get();
        $this->data['todayTitle'] = "Today's Transactions";
        $this->data['todaySubtitle'] = "Check all transactions from the starting of today " . Carbon::today()->format('d/M/Y');
        $this->data['title'] = "Visa Account Page";
        $this->data['todayCols'] = ['Branch', 'Date', 'User', 'Title', 'In', 'Out', 'Comment'];
        $this->data['todayAtts'] = [
            ['foreign' => ['rel' => 'branch', 'att' => 'BRCH_NAME']],
            ['date' => ['att' => 'created_at']],
            ['foreign' => ['rel' => 'dash_user', 'att' => 'DASH_USNM']],
            'VISA_DESC',
            ["number" => ['att' => 'VISA_IN', 'nums' => 2]],
            ["number" => ['att' => 'VISA_OUT', 'nums' => 2]],
            // ["number" => ['att' => 'VISA_BLNC', 'nums' => 2]],
            ["comment" => ['att' => 'VISA_CMNT']],
        ];
        //Trans table
        $this->data['trans'] = Visa::latest300($branch_ID)->get();
        $this->data['transTitle'] = "More Transactions";
        $this->data['transSubtitle'] = "Check Latest 300 visa transaction";
        $this->data['transCols'] = ['Branch', 'Date', 'User', 'Title', 'In', 'Out', 'Comment'];
        $this->data['transAtts'] = [
            ['foreign' => ['rel' => 'branch', 'att' => 'BRCH_NAME']],
            ['date' => ['att' => 'created_at']],
            ['foreign' => ['rel' => 'dash_user', 'att' => 'DASH_USNM']],
            'VISA_DESC',
            ["number" => ['att' => 'VISA_IN', 'nums' => 2]],
            ["number" => ['att' => 'VISA_OUT', 'nums' => 2]],
            // ["number" => ['att' => 'VISA_BLNC', 'nums' => 2]],
            ["comment" => ['att' => 'VISA_CMNT']],
        ];

        $this->data['formURL'] = url('visa/insert');
        $this->data['formTitle'] = "Add Visa Transaction";
        $this->data['balance'] = Visa::currentBalance($branch_ID);
        $this->data['paidToday'] = Visa::paidToday($branch_ID);
        $this->data['collectedToday'] = Visa::collectedToday($branch_ID);
        $this->data['startingBalance'] = Visa::yesterdayBalance($branch_ID);
    }

    public function home()
    {
        $this->setDataArr();
        return view('accounts.visa', $this->data);
    }

    public function insert(Request $request)
    {
        $request->validate([
            "branchID"         => "required|exists:branches,id",
            "title"             => "required",
            "in"                => "required|numeric",
            "out"               => "required|numeric",
        ]);
        Visa::entry($request->branchID, $request->title, $request->in, $request->out, $request->comment);
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
            "branchID"        =>  "required|numeric",
            "from"  =>  "required",
            "to"    =>  "required"
        ]);

        $startDate  = (new DateTime($request->from))->format('Y-m-d 00:00:00');
        $endDate    = (new DateTime($request->to))->format('Y-m-d 23:59:59');

        //query
        $this->data['items'] = Visa::filter(
            $request->branchIDq,
            $startDate,
            $endDate
        );
        $totalOut = $this->data['items']->sum('VISA_OUT');
        $totalIn = $this->data['items']->sum('VISA_IN');
        $diff = $totalIn - $totalOut;

        $this->data['cols'] = ['Branch', 'Date', 'User', 'Title', 'In', 'Out',  'Comment'];

        $this->data['atts'] = [
            ['foreign' => ['rel' => 'branch', 'att' => 'BRCH_NAME']],
            ['date' => ['att' => 'created_at']],
            ['foreign' => ['rel' => 'dash_user', 'att' => 'DASH_USNM']],
            'VISA_DESC',
            ["number" => ['att' => 'VISA_IN', 'nums' => 2]],
            ["number" => ['att' => 'VISA_OUT', 'nums' => 2]],
            // ["number" => ['att' => 'VISA_BLNC', 'nums' => 2]],
            ["comment" => ['att' => 'VISA_CMNT']],
        ];

        //table info
        $this->data['title'] = "FLAWLESS Dashboard";
        $this->data['tableTitle'] = "Visa Report";
        $this->data['tableSubtitle'] = "Showing Visa Transactions from " . (new DateTime($request->from))->format('d-M-Y') . " to " . (new DateTime($request->to))->format('d-M-Y') . ' -- Total Spent: ' . number_format($totalOut) . " Total In: " . number_format($totalIn) . " (In-Out) : " . number_format($diff);

        return view("layouts.table", $this->data);
    }
}
