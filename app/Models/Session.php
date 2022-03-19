<?php

namespace App\Models;

use DateInterval;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class Session extends Model
{
    public const STATE_DONE = "Done";



    public $timestamps = false;
    protected $dates = ['SSHN_DATE'];

    //Query functions
    public function getDiscountAttribute()
    {
        return round($this->SSHN_TOTL * ($this->SSHN_DISC / 100), 2);
    }
    public function getTotalAttribute()
    {
        return $this->SSHN_TOTL - $this->discount;
    }
    public function getDoctorTotalAttribute()
    {
        return round($this->SSHN_DCTR_TOTL * ((100 - $this->SSHN_DISC) / 100), 2);
    }

    public function getRemainingMoneyAttribute()
    {
        return round($this->total - $this->SSHN_PAID - $this->SSHN_PTNT_BLNC, 2);
    }

    public function getRemainingDiscountAttribute()
    {
        return $this->SSHN_TOTL > 0 ? 100 * round((($this->SSHN_TOTL - $this->SSHN_PAID - $this->SSHN_PTNT_BLNC) / $this->SSHN_TOTL), 2) : 0;
    }

    public function getTotalAfterDiscount()
    {
        return $this->total;
    }

    public static function getNewSessions($startDate, $endDate)
    {
        return self::getSessions("asc", "New", $startDate, $endDate);
    }

    public static function getPendingPaymentSessions()
    {
        return self::getSessions("asc", "Pending Payment", null, null);
    }

    public static function getTodaySessions()
    {
        return self::getSessions("asc", null, date('Y-m-d'), date('Y-m-d'));
    }

    public static function getDoneSessions($startDate, $endDate)
    {
        return self::getSessions("desc", "Done", $startDate, $endDate);
    }

    public static function getSessions($order = 'desc', $state = null, $startDate = null, $endDate = null, $patient = null, $doctor = null, $openedBy = null, $moneyBy = null, $totalBegin = null, $totalEnd = null, $isCommision = "0", $loadServices = false)
    {
        $rels = ["doctor", "patient", "creator", "accepter"];
        if ($loadServices)
            array_push($rels, "items.pricelistItem", "items.pricelistItem.device", "items.pricelistItem.area");
        $query = self::with($rels);


        if ($state != null && $state != "All")
            $query = $query->where("SSHN_STTS", "=", $state);

        if ($startDate != null)
            $query = $query->where("SSHN_DATE", ">=", $startDate);

        if ($endDate != null)
            $query = $query->where("SSHN_DATE", "<=", $endDate);

        if ($endDate != null)
            $query = $query->where("SSHN_DATE", "<=", $endDate);

        if ($patient != null && $patient > 0)
            $query = $query->where("SSHN_PTNT_ID", $patient);

        if ($doctor != null && $doctor > 0)
            $query = $query->where("SSHN_DCTR_ID", $doctor);

        if ($openedBy != null && $openedBy > 0)
            $query = $query->where("SSHN_OPEN_ID", $openedBy);

        if ($moneyBy != null && $moneyBy > 0)
            $query = $query->where("SSHN_ACPT_ID", $patient);

        if ($isCommision !== "0")
            $query = $query->where("SSHN_CMSH", $isCommision ? 1 : 0);

        if ($totalBegin != null && is_numeric($totalBegin) && $totalEnd != null && is_numeric($totalEnd))
            $query = $query->whereBetween("SSHN_TOTL", [$totalBegin, $totalEnd]);

        $query = $query->orderBy('SSHN_DATE', $order);

        return  $query->get();
    }

    public static function getDoneCount($startDate, $endDate, $doctorID = null)
    {
        $query = self::where("SSHN_DATE", ">=", $startDate)->where("SSHN_DATE", "<=", $endDate)->where("SSHN_STTS", "Done");
        if ($doctorID != null) {
            $query = $query->where("SSHN_DCTR_ID", $doctorID)->where("SSHN_CMSH", 1);
        }
        return $query->count();
    }

    public static function getDoctorSum($startDate, $endDate, $doctorID = null)
    {
        $query = self::where("SSHN_DATE", ">=", $startDate)->where("SSHN_DATE", "<=", $endDate)->where("SSHN_STTS", "Done")->where("SSHN_CMSH", 1);
        if ($doctorID != null) {
            $query = $query->where("SSHN_DCTR_ID", $doctorID);
        }
        $query = $query->selectRaw("SUM(SSHN_DCTR_TOTL * ((100-SSHN_DISC)/100)) as totalSum")->get();
        return $query->sum('totalSum');
    }

    public static function getPaidSum($startDate, $endDate, $doctorID = null)
    {
        $query = self::where("SSHN_DATE", ">=", $startDate)->where("SSHN_DATE", "<=", $endDate)->where("SSHN_STTS", "Done");
        if ($doctorID != null) {
            $query = $query->where("SSHN_DCTR_ID", $doctorID)->where("SSHN_CMSH", 1);
        }
        return ($query->sum("SSHN_PAID") + $query->sum("SSHN_PTNT_BLNC"));
    }

    public static function getTotalSum($startDate, $endDate, $doctorID = null)
    {
        $query = self::where("SSHN_DATE", ">=", $startDate)->where("SSHN_DATE", "<=", $endDate)->where("SSHN_STTS", "Done");
        if ($doctorID != null) {
            $query = $query->where("SSHN_DCTR_ID", $doctorID)->where("SSHN_CMSH", 1);
        }
        $sum = $query->selectRaw("SUM(SSHN_TOTL * (100-SSHN_DISC/100)) as totalSum")->first();
        return $sum->totalSum;
    }

    public static function getPendingPaymentCount()
    {
        return self::where("SSHN_STTS", "Pending Payment")->count();
    }

    public static function getTodaySessionsCount()
    {
        $today = (new DateTime())->format('Y-m-d');
        return self::where("SSHN_DATE", "=", $today)->count();
    }

    public static function getNewCount($startDate, $endDate)
    {
        return self::where("SSHN_DATE", ">=", $startDate)->where("SSHN_DATE", "<=", $endDate)->where("SSHN_STTS", "New")->count();
    }

    public static function getMinTotal()
    {
        return self::min('SSHN_TOTL');
    }

    public static function getMaxTotal()
    {
        return self::max('SSHN_TOTL');
    }

    public static function createNewSession($patientID, $date, $startTime, $endTime, $comment = null)
    {
        $res = self::insertGetId([
            "SSHN_PTNT_ID"      =>  $patientID,
            "SSHN_DATE"         =>  $date,
            "SSHN_STRT_TIME"    =>  $startTime,
            "SSHN_END_TIME"     =>  $endTime,
            "SSHN_TEXT"         =>  $comment,
            "SSHN_OPEN_ID"      =>  Auth::user()->id,
        ]);
        if ($res) {
            $session = Session::findOrFail($res);
            $session->logEvent("Created Session");
            return 1;
        } else return 0;
    }

    ///services
    public function addService($pricelistID, $unit, $note = null, $recalculateTotal = true, $isDoctor = true)
    {
        if ($this->canEditServices())
            DB::transaction(function () use ($pricelistID, $unit, $note, $recalculateTotal, $isDoctor) {
                $pricelistItem = PriceListItem::findOrFail($pricelistID);
                $this->items()->create([
                    "SHIT_PLIT_ID"  =>  $pricelistItem->id,
                    "SHIT_PRCE"     =>  $pricelistItem->PLIT_PRCE,
                    "SHIT_NOTE"     =>  $note,
                    "SHIT_QNTY"     =>  $unit,
                    "SHIT_DCTR"     =>  $isDoctor ? 1 : 0,
                    "SHIT_TOTL"     =>  $unit * $pricelistItem->PLIT_PRCE,
                ]);
                if ($this->save()) {
                    $this->logEvent("Added Service, device: " . $pricelistItem->device->DVIC_NAME);
                }
                if ($recalculateTotal)
                    $this->calculateTotal();
            });
    }

    public function payFromPatientPackages()
    {
        if ($this->canEditMoney())
            DB::transaction(function () {
                foreach ($this->items()->uncollected()->get() as $item) {
                    $item->loadMissing("pricelistItem");
                    $foundPackages = $this->patient->hasPackage($item->pricelistItem);
                    if ($foundPackages > 0) {
                        $packagesToUse = min($item->SHIT_QNTY, $foundPackages);
                        $itemsPrice =  $this->patient->usePackage($item->pricelistItem, $packagesToUse);
                        $this->SSHN_PTNT_BLNC +=  $itemsPrice;
                        $item->SHIT_PRCE = $itemsPrice / $packagesToUse;
                        $item->SHIT_TOTL = $item->SHIT_PRCE * $item->SHIT_QNTY;
                        $item->SHIT_CLTD_PCKG = 1;
                        $item->save();
                        $this->save();
                    }
                }
                $this->calculateTotal();
                if ($this->save()) {
                    $this->logEvent("Settled Packages from client packages ");
                }
            });
    }

    public function payFromPatientBalance()
    {
        if ($this->canEditMoney())
            DB::transaction(function () {

                $remainingMoney = $this->remaining_money;
                $this->SSHN_PTNT_BLNC = $this->SSHN_PTNT_BLNC + $remainingMoney;
                $this->SSHN_ACPT_ID = Auth::user()->id;
                $this->patient->deductBalance($remainingMoney, $this->id);
                if ($this->save()) {
                    $this->logEvent("Settled Amount ({$remainingMoney}) from client balance ");
                }
            });
    }

    public function addPayment($amount, $isCash = true)
    {

        $remainingMoney = $this->remaining_money;
        if ($this->canEditMoney())
            DB::transaction(function () use ($amount, $remainingMoney, $isCash) {
                if ($amount > $remainingMoney) {
                    $this->SSHN_ACPT_ID = Auth::user()->id;
                    $this->SSHN_PAID = $this->SSHN_PAID + $remainingMoney;
                    $extra = $amount - $remainingMoney;
                    $this->patient->pay($extra, "Extra Cash Entry from Session#{$this->id}", false);
                    if ($this->save()) {
                        $this->logEvent(($isCash) ? 'Cash' : 'Visa' . " paid: {$amount} , Extra amount ({$extra}) added to patient balance ");
                    }
                } elseif ($amount <= $remainingMoney) {
                    $this->SSHN_ACPT_ID = Auth::user()->id;
                    $this->SSHN_PAID = $this->SSHN_PAID + $amount;
                    if ($this->save()) {
                        $this->logEvent(($isCash) ? 'Cash' : 'Visa' . " paid: {$amount} ");
                    }
                }
                $transTitle = "Recieved from " . $this->patient->PTNT_NAME;
                if ($isCash) {
                    $this->SSHN_PYMT_TYPE = "Cash";
                    $this->save();
                    Cash::entry($transTitle, $amount, 0, "Automated Cash Entry for Session#{$this->id}");
                } else {
                    $this->SSHN_PYMT_TYPE = "Visa";
                    $this->save();
                    Visa::entry($transTitle, $amount, 0, "Automated Visa Entry for Session#{$this->id}");
                }
            });
    }

    public function clearServices()
    {
        if ($this->canEditServices())
            $this->items()->delete();
    }

    public function setCommission($isCommission)
    {
        if ($this->canEditServices())
            DB::transaction(function () use ($isCommission) {
                $this->SSHN_CMSH = $isCommission;
                if ($this->save()) {
                    $this->logEvent("set Commission to " . ($isCommission ? 'True' : 'False'));
                }
            });
    }

    public function setDiscount($discount)
    {
        if ($this->canEditMoney())
            DB::transaction(function () use ($discount) {
                $this->SSHN_DISC = $discount;
                if ($this->save()) {
                    $this->logEvent("set Discount to " . $discount);
                }
            });
    }

    public function calculateTotal()
    {
        $total = 0;
        $doctorTotal = 0;
        foreach ($this->items as $item) {
            $total += $item->SHIT_TOTL;
            if ($item->is_doctor)
                $doctorTotal += $item->SHIT_TOTL;
        }
        $this->SSHN_TOTL = $total;
        $this->SSHN_DCTR_TOTL = $doctorTotal;
        $this->save();
    }

    public function deleteSession()
    {
        DB::transaction(function () {
            $this->items()->delete();
            $this->logs()->delete();
            $this->feedback()->delete();

            if ($this->SSHN_PAID > 0)
                if ($this->SSHN_PYMT_TYPE == "Cash") {
                    Cash::entry("Session#{$this->id} deleted", 0, $this->SSHN_PAID, "Added Automatically after session delete");
                } elseif ($this->SSHN_PYMT_TYPE == "Visa") {
                    Visa::entry("Session#{$this->id} deleted", 0, $this->SSHN_PAID, "Added Automatically after session delete");
                }

            if ($this->SSHN_PTNT_BLNC > 0)
                $this->patient->pay($this->SSHN_PTNT_BLNC, "Money Refund after Session delete", false);

            $this->delete();
        });
    }

    public function assignTo($doctorID)
    {
        if ($this->canEditDoctor())
            DB::transaction(function () use ($doctorID) {
                $this->SSHN_DCTR_ID = $doctorID;
                if ($this->save()) {
                    $doctor = DashUser::findOrFail($doctorID);
                    $this->logEvent("Assigned Doctor '{$doctor->DASH_USNM}'");
                }
            });
    }

    public function logEvent($text)
    {
        $logEvent = new Log([
            "LOG_TEXT" => $text, "LOG_DASH_ID" => Auth::user()->id
        ]);
        $this->logs()->save($logEvent);
    }

    ////set states
    public function setAsNew()
    {
        if ($this->canBeNew()) {
            $this->SSHN_STTS = "New";
            $this->save();
            $this->logEvent("Set Session as New");
        }
    }

    public function setAsPendingPayment()
    {
        if ($this->canBePending()) {
            $this->SSHN_STTS = "Pending Payment";
            $this->save();
            $this->logEvent("Set Session as Pending Payment");
        }
    }

    public function setAsDone()
    {
        if ($this->canBeDone()) {
            $this->SSHN_STTS = "Done";
            $this->save();
            $this->logEvent("Set Session as DONE :)");
            $this->createFeedback();
        }
    }

    public function createFeedback()
    {
        Feedback::createFeedback($this->id, $this->SSHN_DATE->add(new DateInterval('P5D'))->format('Y-m-d'));
    }

    public function returnCollectedPackages(){
        $this->loadMissing("patient");
        $itemsToReturn = $this->items()->collected()->get();
        foreach($itemsToReturn as $item){
            $this->patient->addPackage($item->id, $item->SHIT_QNTY, $item->SHIT_PRCE);
        }
    }

    public function setAsCancelled($comment = null)
    {
        if ($this->canBeCancelled()) {
            $this->returnCollectedPackages();
            $this->SSHN_STTS = "Cancelled";
            if ($comment !== null)
                $this->SSHN_TEXT = $this->SSHN_TEXT . ". Cancellation Note: " . $comment;
            if ($this->save()) {
                $this->logEvent("Set Session as Cancelled");
                return true;
            }
        }
        return false;
    }

    ////privilages
    public function canEditInfo()
    {
        return ($this->SSHN_STTS == "New" || $this->SSHN_STTS == "Pending Payment");
    }

    public function canEditServices()
    {
        return ($this->SSHN_STTS == "New" || $this->SSHN_STTS == "Pending Payment");
    }

    public function canEditDoctor()
    {
        return (Auth::user()->isAdmin() || $this->SSHN_STTS == "New");
    }

    public function canEditMoney()
    {
        return ($this->SSHN_STTS == "New" || $this->SSHN_STTS == "Pending Payment");
    }

    public function canBeNew()
    {
        return ($this->SSHN_STTS != "New" && $this->SSHN_STTS != "Done");
    }

    public function canBePending()
    {
        return ($this->SSHN_STTS != "Pending Payment" && ($this->SSHN_STTS == "New" && $this->SSHN_TOTL > 0));
    }

    public function canBeCancelled()
    {
        return ($this->SSHN_STTS == "New" && !$this->SSHN_TOTL > 0);
    }

    public function canBeDone()
    {
        return ($this->SSHN_STTS != "Done" && $this->SSHN_DCTR_ID != null && (($this->SSHN_STTS == "New" || $this->SSHN_STTS == "Pending Payment") && $this->SSHN_TOTL > 0 && $this->remaining_money <= 0));
    }

    /////relations
    function patient()
    {
        return $this->belongsTo("App\Models\Patient", "SSHN_PTNT_ID");
    }

    function creator()
    {
        return $this->belongsTo("App\Models\DashUser", "SSHN_OPEN_ID");
    }

    function doctor()
    {
        return $this->belongsTo("App\Models\DashUser", "SSHN_DCTR_ID");
    }

    function accepter()
    {
        return $this->belongsTo("App\Models\DashUser", "SSHN_ACPT_ID");
    }

    function feedback()
    {
        return $this->hasOne("App\Models\Feedback", "FDBK_SSHN_ID");
    }

    function items()
    {
        return $this->hasMany(SessionItem::class, "SHIT_SSHN_ID");
    }

    function logs()
    {
        return $this->hasMany("App\Models\Log", "LOG_SSHN_ID");
    }
}
