<?php

namespace App\Http\Controllers;

use App\Models\Cash;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;

class CashController extends Controller
{
    protected $data;

    private function setDataArr()
    {
        //Trans table
        $this->data['todayTrans'] = Cash::with("dash_user")->whereDate('created_at', Carbon::today())->orderByDesc('id')->get();
        $this->data['todayTitle'] = "Today's Transactions";
        $this->data['todaySubtitle'] = "Check all transactions from the starting of today " . Carbon::today()->format('d/M/Y');
        $this->data['title'] = "Cash Account Page";
        $this->data['todayCols'] = ['Date', 'User', 'Title', 'In', 'Out', 'Balance', 'Comment'];
        $this->data['todayAtts'] = [
            ['date' => ['att' => 'created_at']],
            ['foreign' => ['rel' => 'dash_user', 'att' => 'DASH_USNM']],
            'CASH_DESC',
            ["number" => ['att' => 'CASH_IN', 'nums' => 2]],
            ["number" => ['att' => 'CASH_OUT', 'nums' => 2]],
            ["number" => ['att' => 'CASH_BLNC', 'nums' => 2]],
            ["comment" => ['att' => 'CASH_CMNT']],
        ];
        //Trans table
        $this->data['trans'] = Cash::with("dash_user")->orderByDesc('id')->limit(300)->get();
        $this->data['transTitle'] = "More Transactions";
        $this->data['transSubtitle'] = "Check Latest 300 cash transaction";
        $this->data['transCols'] = ['Date', 'User', 'Title', 'In', 'Out', 'Balance', 'Comment'];
        $this->data['transAtts'] = [
            ['date' => ['att' => 'created_at']],
            ['foreign' => ['rel' => 'dash_user', 'att' => 'DASH_USNM']],
            'CASH_DESC',
            ["number" => ['att' => 'CASH_IN', 'nums' => 2]],
            ["number" => ['att' => 'CASH_OUT', 'nums' => 2]],
            ["number" => ['att' => 'CASH_BLNC', 'nums' => 2]],
            ["comment" => ['att' => 'CASH_CMNT']],
        ];

        $this->data['formURL'] = url('cash/insert');
        $this->data['formTitle'] = "Add Cash Transaction";
        $this->data['balance'] = Cash::currentBalance();
        $this->data['paidToday'] = Cash::paidToday();
        $this->data['collectedToday'] = Cash::collectedToday();
        $this->data['startingBalance'] = Cash::yesterdayBalance();
    }

    public function home()
    {
        $this->setDataArr();
        return view('accounts.cash', $this->data);
    }

    public function insert(Request $request)
    {
        $request->validate([
            "title"              => "required",
            "in"              => "required|numeric",
            "out"              => "required|numeric",
        ]);
        Cash::entry($request->title, $request->in, $request->out, $request->comment);
        return redirect("cash/home");
    }

    public function query()
    {
        $this->data['title']        = "Cash Report";
        $this->data['formTitle']    = "Prepare Cash Report";
        $this->data['formSubtitle']      = 'View Cash Transactions from Start Date till End Date';
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
        $this->data['items'] = Cash::with("dash_user")->whereBetween('created_at', [$startDate, $endDate])->orderByDesc('id')->get();
        $totalOut = $this->data['items']->sum('CASH_OUT');

        $this->data['cols'] = ['Date', 'User', 'Title', 'In', 'Out', 'Balance', 'Comment'];

        $this->data['atts'] = [
            ['date' => ['att' => 'created_at']],
            ['foreign' => ['rel' => 'dash_user', 'att' => 'DASH_USNM']],
            'CASH_DESC',
            ["number" => ['att' => 'CASH_IN', 'nums' => 2]],
            ["number" => ['att' => 'CASH_OUT', 'nums' => 2]],
            ["number" => ['att' => 'CASH_BLNC', 'nums' => 2]],
            ["comment" => ['att' => 'CASH_CMNT']],
        ];

        //table info
        $this->data['title'] = "FLAWLESS Dashboard";
        $this->data['tableTitle'] = "Cash Report";
        $this->data['tableSubtitle'] = "Showing Cash Transactions from " . (new DateTime($request->from))->format('d-M-Y') . " to " . (new DateTime($request->to))->format('d-M-Y') . " -- Total Spent: " .   $totalOut;

        return view("layouts.table", $this->data);
    }
}
