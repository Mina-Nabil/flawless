<?php

namespace App\Models;

use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Patient extends Model
{
    protected $table = "patients";
    public $timestamps = true;


    public function sessions()
    {
        return $this->hasMany("App\Models\Session", "SSHN_PTNT_ID", "id");
    }

    public function services()
    {
        return $this->hasManyThrough(SessionItem::class, "App\Models\Session", "SSHN_PTNT_ID", "SHIT_SSHN_ID");
    }

    public function pricelist()
    {
        return $this->belongsTo("App\Models\PriceList", "PTNT_PRLS_ID");
    }

    public function channel()
    {
        return $this->belongsTo("App\Models\Channel", "PTNT_CHNL_ID");
    }

    public function totalPaid()
    {
        return DB::table('sessions')->where('SSHN_PTNT_ID', $this->id)
            ->selectRaw('SUM(SSHN_PAID) as paid, SUM(SSHN_DISC) as discount')
            ->get()->first()->paid ?? 0;
    }

    public function totalDiscount()
    {
        return DB::table('sessions')->where('SSHN_PTNT_ID', $this->id)->where('SSHN_STTS', "Done")
            ->selectRaw('SUM(SSHN_PAID) as paid, SUM(SSHN_DISC) as discount')
            ->get()->first()->discount ?? 0;
    }

    public function servicesTaken()
    {
        return [];
        return DB::table('sessions')->where('SSHN_PTNT_ID', $this->id)->where('SSHN_STTS_ID', 4)
            ->join('service_items', "SRVC_SSHN_ID", '=', 'orders.id')
            ->join('inventory', "SRVC_INVT_ID", '=', 'inventory.id')
            ->join('products', "INVT_PROD_ID", '=', 'products.id')
            ->selectRaw('SUM(SRVC_KGS) as SRVC_KGS, PROD_NAME, SRVC_PRCE')
            ->groupBy('order_items.id')
            ->get();
    }
    ///////stats

    public static function getPatientsCountCreatedThisMonth()
    {
        $startOfMonth = (new DateTime('now'))->format('Y-m-01');
        $endOfMonth = (new DateTime('now'))->format('Y-m-t');
        return DB::table('patients')->whereBetween("created_at", [$startOfMonth, $endOfMonth])->count();
    }

    public function deductBalance($moneyToDeducted, $sessionID)
    {
        $this->pay(-1 * $moneyToDeducted, "Session#{$sessionID} Settle from balance", false, false);
    }

    ///////transactions

    public function payments()
    {
        return $this->hasMany('App\Models\PatientPayment', 'PTPY_PTNT_ID');
    }

    public function pay($amount, $comment = null, $addEntry = true, $isVisa = false)
    {
        DB::transaction(function () use ($amount, $comment, $addEntry, $isVisa) {
            $this->PTNT_BLNC += $amount;
            $payment =  new PatientPayment();
            $payment->PTPY_PAID = $amount;
            $payment->PTPY_CMNT = $comment;
            $payment->PTPY_BLNC = $this->PTNT_BLNC;
            $payment->PTPY_TYPE = ($isVisa) ? "Visa" : "Cash";
            $payment->PTPY_DASH_ID = Auth::user()->id;

            $this->payments()->save($payment);
            if ($addEntry) {
                $entryTitle = "Recieved from " . $this->PTNT_NAME;
                if (!$isVisa) {
                    Cash::entry($entryTitle, $amount, 0, $comment);
                } else {
                    Visa::entry($entryTitle, $amount, 0, $comment);
                }
            }
            $this->save();
        });
    }
}
