<?php

namespace App\Models;

use DateInterval;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Patient extends Model
{
    protected $table = "patients";
    public $timestamps = true;

    public function profileURL()
    {
        return url('patients/profile/' . $this->id);
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

    public static function loadMissingPatients($daysFrom, $daysTo)
    {
        $recentPatientsIDs = self::join("sessions", "SSHN_PTNT_ID", '=', "patients.id")->whereRaw("SSHN_DATE < DATE_SUB(NOW() , INTERVAL {$daysFrom} DAY) AND SSHN_DATE > DATE_SUB(NOW() , INTERVAL {$daysTo} DAY ")->selectRaw('DISTINCT patients.id')->get()->pluck('id');
        return self::join("sessions", "SSHN_PTNT_ID", '=', "patients.id")->selectRaw("patients.*, Count(sessions.id) as sessionCount")->groupBy('patients.id')->whereNotIn('patients.id', $recentPatientsIDs)->get();
    }

    public static function getTopPayers($limit)
    {
        return self::join("sessions", "sessions.SSHN_PTNT_ID", "=", "patients.id")
            ->select("patients.*")
            ->selectRaw("SUM(SSHN_TOTL - (SSHN_DISC/100*SSHN_TOTL)) as total_paid")
            ->selectRaw("COUNT(sessions.id) as sessions_count")
            ->where('SSHN_STTS', "Done")
            ->havingRaw("SUM(SSHN_TOTL - (SSHN_DISC/100*SSHN_TOTL)) >= {$limit}")
            ->groupBy("patients.id")
            ->get();
    }

    public function createAFollowUp($updateLatestIfExist = true)
    {
        if ($updateLatestIfExist)
            $this->followUps()->updateOrCreate([], [
                "FLUP_DATE" => (new DateTime())->add(new DateInterval("P3M"))->format('Y-m-d')
            ]);
        else {
            $this->followUps()->create([], [
                "FLUP_DATE" => (new DateTime())->add(new DateInterval("P3M"))->format('Y-m-d')
            ]);
        }
    }

    /////package functions

    /**
     * @param PriceListItem item to be queried if available
     * @return int pricelist item quantity found
     */
    public function hasPackage(PriceListItem $item): int
    {
        return $this->packageItems()->where("PTPK_PLIT_ID", $item->id)->sum("PTPK_QNTY") ?? 0;
    }

    /**
     * @param PriceListItem item to be queried if available
     * @return int pricelist item quantity found
     */
    public function addPackage($itemID, int $quantity, float $price): bool
    {
        return $this->packageItems()->create([
            "PTPK_PLIT_ID"  =>  $itemID,
            "PTPK_QNTY"     =>  $quantity,
            "PTPK_PRCE"     =>  $price,
        ]) == true;
    }

    /**
     * @param PriceListItem item to be queried if available
     * @return float price of deducted items
     */
    public function usePackage(PriceListItem $item, int $quantity): float
    {
        $totalDeducted = 0;
        for ($i = $quantity; $i > 0; $i--) {
            $packagesFound = $this->packageItems()->where("PTPK_PLIT_ID", $item->id)->get();
            foreach ($packagesFound as $package)
                if ($package != null && $package->PTPK_QNTY > 0) {
                    $package->PTPK_QNTY = $package->PTPK_QNTY - 1;
                    $totalDeducted = $totalDeducted + $package->PTPK_PRCE;
                    $package->save();
                }
        }
        return $totalDeducted;
    }

    ///////relations

    public function payments(): HasMany
    {
        return $this->hasMany(PatientPayment::class, 'PTPY_PTNT_ID');
    }

    public function balanceLogs(): HasMany
    {
        return $this->hasMany(BalanceLog::class, 'BLLG_PTNT_ID');
    }

    public function packageItems(): HasMany
    {
        return $this->hasMany(PatientPackage::class, 'PTPK_PTNT_ID');
    }

    function followUps()
    {
        return $this->hasMany(FollowUp::class, "FLUP_PTNT_ID");
    }


    public function sessions()
    {
        return $this->hasMany(Session::class, "SSHN_PTNT_ID", "id");
    }

    public function services()
    {
        return $this->hasManyThrough(SessionItem::class, Session::class, "SSHN_PTNT_ID", "SHIT_SSHN_ID");
    }

    public function pricelist()
    {
        return $this->belongsTo("App\Models\PriceList", "PTNT_PRLS_ID");
    }

    public function channel()
    {
        return $this->belongsTo("App\Models\Channel", "PTNT_CHNL_ID");
    }

    //transactions

    public function pay($amount, $comment = null, $addEntry = true, $isVisa = false, $updateBalance = true)
    {
        DB::transaction(function () use ($amount, $comment, $addEntry, $isVisa, $updateBalance) {
            if ($updateBalance)
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

    public function addBalance($title, $amount, $comment = null)
    {
        $userID = Auth::user()->id;
        DB::transaction(function () use ($title, $amount, $comment, $userID) {
            $this->PTNT_BLNC += $amount;
            $this->balanceLogs()->create([
                "BLLG_TTLE"     =>  $title,
                "BLLG_DASH_ID"  =>  $userID,
                "BLLG_IN"       => ($amount >= 0) ? $amount : 0,
                "BLLG_OUT"      => ($amount < 0) ? -1 * $amount : 0,
                "BLLG_CMNT"     =>  $comment,
            ]);
            $this->save();
        });
    }
}
