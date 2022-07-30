<?php

namespace App\Models;

use Carbon\Carbon;
use DateInterval;
use DateTime;
use Exception;
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
            ->selectRaw('SUM(SSHN_PAID + SSHN_PTNT_BLNC) as paid, SUM(SSHN_DISC) as discount')
            ->get()->first()->paid ?? 0;
    }

    public function totalDiscount()
    {
        return DB::table('sessions')->where('SSHN_PTNT_ID', $this->id)->where('SSHN_STTS', "Done")
            ->selectRaw(' SUM(SSHN_TOTL * (SSHN_DISC/100)) as discount')
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

    public function deductBalance($branchID, $moneyToDeducted, $sessionID)
    {
        $this->pay($branchID, -1 * $moneyToDeducted, "Session#{$sessionID} Settle from balance", false, false);
    }

    public static function loadMissingPatients($daysFrom, $daysTo)
    {
        $recentPatientsIDs = self::join("sessions", "SSHN_PTNT_ID", '=', "patients.id")->whereRaw("SSHN_DATE < DATE_SUB(NOW() , INTERVAL {$daysFrom} DAY) AND SSHN_DATE > DATE_SUB(NOW() , INTERVAL {$daysTo} DAY ")->selectRaw('DISTINCT patients.id')->get()->pluck('id');
        return self::join("sessions", "SSHN_PTNT_ID", '=', "patients.id")->selectRaw("patients.*, Count(sessions.id) as sessionCount")->groupBy('patients.id')->whereNotIn('patients.id', $recentPatientsIDs)->get();
    }

    public static function loadByBranch($branchID)
    {
        return DB::table('patients', 'p1')->join("sessions", "SSHN_PTNT_ID", '=', "p1.id")
            ->whereRaw("sessions.id = 
                    (SELECT id from sessions where sessions.SSHN_PTNT_ID = p1.id AND sessions.SSHN_BRCH_ID = {$branchID} ORDER BY id asc limit 1)")
            ->get();
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

    public static function getPatientsByDate($from, $to)
    {
        return self::whereBetween("created_at", [$from, $to])->get();
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

    public function submitNewPackage($branch, $item_id, $quantity, $price, $isVisa): bool
    {
        try {
            DB::transaction(function () use ($item_id, $quantity, $price, $isVisa, $branch) {
                $this->addPackage($item_id, $quantity, $price);
                $this->pay($branch, $quantity * $price, "Payment added from adding package", true, $isVisa, false);
            });
        } catch (Exception $e) {
            report($e);
            return false;
        }
        return true;
    }

    public function getAvailablePackagesAttribute()
    {
        return $this->packageItems()->with("pricelistItem", "pricelistItem.area", "pricelistItem.device")->where("PTPK_QNTY", ">", "0")->get();
    }

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
    private function addPackage($itemID, int $quantity, float $price): bool
    {
        try {
            /** @var PriceListItem */
            $item = PriceListItem::with('device', 'area')->findOrFail($itemID);
            $title = "Adding Package";
            $comment = "Adding " . $quantity . " " . $item->device->DVIC_NAME . " " . $item->PLIT_TYPE;
            if ($item->area != null) {
                $comment .= " (" . $item->area->AREA_NAME . ")";
            }
            $comment .= " for " . $price . "EGP";
            DB::transaction(function () use ($itemID, $quantity, $price, $title, $comment) {
                $this->packageItems()->create([
                    "PTPK_PLIT_ID"  =>  $itemID,
                    "PTPK_QNTY"     =>  $quantity,
                    "PTPK_PRCE"     =>  $price,
                ]);
                $this->addToPackageLog($title, $quantity, $comment);
            });
            return true;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    /**
     * @param $title should include session number or transaction
     * @param $comment should include package name and price
     */
    private function addToPackageLog(string $title, int $amount, string $comment, int $sessionID = null): bool
    {
        return $this->packageLogs()->create([
            "PKLG_TTLE"     =>  $title,
            "PKLG_DASH_ID"  =>  Auth::user()->id,
            "PKLG_SSHN_ID"  =>  $sessionID,
            "PKLG_AMNT"     =>  $amount,
            "PKLG_CMNT"     =>  $comment,
        ]) !== null;
    }

    /**
     * @param PriceListItem item to be queried if available
     * @return float price of deducted items
     */
    public function usePackage(PriceListItem $item, int $quantity, int $sessionID): float
    {
        $totalDeducted = 0;
        $itemAvailability = $this->packageItems()->sum("PTPK_QNTY");
        while ($quantity > 0 && $itemAvailability > 0) {
            $package = $this->packageItems()->where([
                ["PTPK_PLIT_ID", $item->id],
                ["PTPK_QNTY", ">", 0]
            ])->first();

            if ($package != null) {
                $toDeduct = min($quantity, $package->PTPK_QNTY);
                $package->PTPK_QNTY = $package->PTPK_QNTY - $toDeduct;
                $quantity = $quantity - $toDeduct;
                $itemAvailability = $itemAvailability - $toDeduct;
                $totalDeducted = $totalDeducted + ($package->PTPK_PRCE * $toDeduct);
                $package->save();
                $title = "Using Package";
                $comment = "Using " . $toDeduct . " " . $item->device->DVIC_NAME . " " . $item->PLIT_TYPE;
                if ($item->area != null) {
                    $comment .= " (" . $item->area->AREA_NAME . ")";
                }
                $comment .= " for " . $package->PTPK_PRCE . 'EGP';
                $this->addToPackageLog($title, $toDeduct, $comment, $sessionID);
            }
        }
        return $totalDeducted;
    }

    public function setNote($note): bool
    {
        $this->PTNT_NOTE = $note;
        try {
            return $this->save();
        } catch (Exception $e) {
            report($e);
            return false;
        }
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

    public function packageLogs(): HasMany
    {
        return $this->hasMany(PackageLog::class, 'PKLG_PTNT_ID');
    }

    public function packageItems(): HasMany
    {
        return $this->hasMany(PatientPackage::class, 'PTPK_PTNT_ID')->orderBy("PTPK_QNTY", "desc");
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

    public function location()
    {
        return $this->belongsTo("App\Models\Location", "PTNT_LOCT_ID");
    }

    //transactions
    public function pay($branchID, $amount, $comment = null, $addEntry = true, $isVisa = false, $updateBalance = true)
    {
        DB::transaction(function () use ($branchID, $amount, $comment, $addEntry, $isVisa, $updateBalance) {
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
                    Cash::entry($branchID, $entryTitle, $amount, 0, $comment);
                } else {
                    Visa::entry($branchID, $entryTitle, $amount, 0, $comment);
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

    public function scopeLoadBy($query, $channel_ids, $locations_ids, $from = null, $to = null)
    {
        if (!in_array(-1, $locations_ids)) {
            $query = $query->where(function ($query) use ($locations_ids) {
                foreach ($locations_ids as $loct_id)
                    $query->orWhere('PTNT_LOCT_ID', '=', $loct_id);
            });
        }
        if (!in_array(-1, $channel_ids)) {
            $query = $query->where(function ($query) use ($channel_ids) {
                foreach ($channel_ids as $channel_id)
                    $query->orWhere('PTNT_CHNL_ID', '=', $channel_id);
            });
        }
        if ($from != null) {
            $fromDate = new Carbon($from);
            $query = $query->whereDate('created_at', ">=", $fromDate->format('Y-m-d'));
        }

        if ($to != null) {
            $toDate = new Carbon($to);
            $query = $query->whereDate('created_at', "<=", $toDate->format('Y-m-d'));
        }

        return $query;
    }
}
