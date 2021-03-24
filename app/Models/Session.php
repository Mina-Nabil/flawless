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
    public $timestamps = false;
    private $remainingMoney;
    protected $dates = ['SSHN_DATE'];

    //Query functions

    public function getRemainingMoney()
    {
        if (isset($this->remainingMoney)) return $this->remainingMoney;

        $this->remainingMoney = $this->SSHN_TOTL - $this->SSHN_DISC - $this->SSHN_PAID - $this->SSHN_PTNT_BLNC;
        return $this->remainingMoney;
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

    public static function getSessions($order = 'desc', $state = null, $startDate = null, $endDate = null, $patient = null, $doctor = null, $openedBy = null, $moneyBy = null, $totalBegin = null, $totalEnd = null, $isCommision = null)
    {
        $query = self::with("doctor", "patient", "creator", "accepter");


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

        if ($isCommision != null && $isCommision != 0)
            $query = $query->where("SSHN_CMSH", $isCommision);

        if ($totalBegin != null && is_numeric($totalBegin) && $totalEnd != null && is_numeric($totalEnd))
            $query = $query->whereBetween("SSHN_TOTL", [$totalBegin, $totalEnd]);

        $query = $query->orderBy('SSHN_DATE', $order);

        return  $query->get();
    }

    public static function getDoneCount($startDate, $endDate)
    {
        return self::where("SSHN_DATE", ">=", $startDate)->where("SSHN_DATE", "<=", $endDate)->where("SSHN_STTS", "Done")->count();
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
            $today = (new DateTime());
            $twoDaysBeforeSession = (new DateTime($date))->sub(new DateInterval('P2D'));
            if ($twoDaysBeforeSession > $today)
                $session->createFollowup();
            return 1;
        } else return 0;
    }

    ///services
    public function addService($pricelistID, $unit, $recalculateTotal = true)
    {
        if ($this->canEditServices())
            DB::transaction(function () use ($pricelistID, $unit, $recalculateTotal) {
                $pricelistItem = PriceListItem::findOrFail($pricelistID);
                $this->items()->create([
                    "SHIT_PLIT_ID"  =>  $pricelistItem->id,
                    "SHIT_PRCE"     =>  $pricelistItem->PLIT_PRCE,
                    "SHIT_QNTY"     =>  $unit,
                    "SHIT_TOTL"     =>  $unit * $pricelistItem->PLIT_PRCE,
                ]);
                if ($this->save()) {
                    $this->logEvent("Added Service, device: " . $pricelistItem->device->DVIC_NAME);
                }
                if ($recalculateTotal)
                    $this->calculateTotal();
            });
    }

    public function payFromPatientBalance()
    {
        if ($this->canEditMoney())
            DB::transaction(function () {
                $remainingMoney = $this->getRemainingMoney();
                $amountToDeduct = $remainingMoney;

                $this->SSHN_PTNT_BLNC = $this->SSHN_PTNT_BLNC + $remainingMoney;
                $this->SSHN_ACPT_ID = Auth::user()->id;
                $this->patient->deductBalance($amountToDeduct, $this->id);
                if ($this->save()) {
                    $this->logEvent("Settled Amount ({$amountToDeduct}) from client balance ");
                }
            });
    }

    public function addPayment($amount, $isCash = true)
    {

        $remainingMoney = $this->getRemainingMoney();
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
                    Cash::entry($transTitle, $amount, 0, "Automated Cash Entry for Session#{$this->id}");
                } else {
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
        foreach ($this->items as $item) {
            $total += $item->SHIT_TOTL;
        }
        $this->SSHN_TOTL = $total;
        $this->save();
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

    public function createFollowup($comment = null)
    {
        FollowUp::createFollowup($this->id, $this->SSHN_DATE->sub(new DateInterval('P2D'))->format('Y-m-d'), $comment);
    }

    public function createFeedback()
    {
        Feedback::createFeedback($this->id, $this->SSHN_DATE->add(new DateInterval('P5D'))->format('Y-m-d'));
    }

    public function setAsCancelled($comment = null)
    {
        if ($this->canBeCancelled()) {
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
        return ($this->SSHN_STTS == "New");
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
        return ($this->SSHN_STTS != "Done" && $this->SSHN_DCTR_ID != null && (($this->SSHN_STTS == "New" || $this->SSHN_STTS == "Pending Payment") && $this->SSHN_TOTL > 0 && $this->getRemainingMoney() <= 0));
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

    function items()
    {
        return $this->hasMany("App\Models\SessionItem", "SHIT_SSHN_ID");
    }

    function logs()
    {
        return $this->hasMany("App\Models\Log", "LOG_SSHN_ID");
    }
}
