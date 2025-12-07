<?php

namespace App\Models;

use App\Helpers\SmsHandler;
use App\Jobs\SendSMSJob;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as LaravelLog;

class Session extends Model
{
    public const STATE_DONE = "Done";
    public const STATE_NEW = "New";
    public const STATE_PENDING_PYMT = "Pending Payment";
    public const STATE_CANCELLED = "Cancelled";
    public const STATE_LATE_CANCEL = "Late Cancel";

    public const ACTIVE_STATES = [
        self::STATE_NEW, self::STATE_PENDING_PYMT
    ];

    public $timestamps = false;
    protected $dates = ['SSHN_DATE'];

    //Query functions
    public function getClassNameAttribute()
    {
        switch ($this->SSHN_STTS) {
            case 'New':
                return "bg-info";

            case 'Pending Payment':
                return "bg-dark";

            case 'Cancelled':
                return "bg-danger";

            case 'Late Cancel':
                return "bg-danger";

            case 'No Show':
                return "bg-danger";

            case 'Done':
                return "bg-success";

            default:
                return "bg-info";
        }
    }
    public function getEventColorAttribute()
    {
        $this->loadMissing('pricelistItems');
        LaravelLog::info(print_r($this->pricelistItems->toArray(), true));
        foreach ($this->pricelistItems as $price_item) {
            if ($price_item->PLIT_DVIC_ID == 1) return '#055C9D';
            elseif ($price_item->PLIT_DVIC_ID == 2) return '#A020F0';
            elseif ($price_item->PLIT_DVIC_ID == 19) return '#D1D100';
            elseif ($price_item->PLIT_DVIC_ID == 9) return '#7B7B7B';
            elseif (in_array($price_item->PLIT_DVIC_ID, [20, 7, 18, 23, 22])) return '#24E500';
            else return '#FF0D86';
        }
        return '#FF0D86';
    }

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

    public function getCarbonDateAttribute()
    {
        $timeArr = explode(':', $this->SSHN_STRT_TIME);
        return (new Carbon($this->SSHN_DATE))->SetTime($timeArr[0], $timeArr[1], $timeArr[2]);
    }

    public function getCarbonStartTimeAttribute()
    {
        $timeArr = explode(':', $this->SSHN_STRT_TIME);
        return ((new Carbon($this->SSHN_DATE))->SetTime($timeArr[0], $timeArr[1], $timeArr[2]));
    }

    public function getCarbonEndTimeAttribute()
    {
        $timeArr = explode(':', $this->SSHN_END_TIME);
        return ((new Carbon($this->SSHN_DATE))->SetTime($timeArr[0], $timeArr[1], $timeArr[2]));
    }

    public function getTotalAfterDiscount()
    {
        return $this->total;
    }

    public static function getNewSessions($branchID, $startDate, $endDate, $userID = null)
    {
        return self::getSessions($branchID, null, "asc", ["New"], $startDate, $endDate, null, null, $userID);
    }

    public static function getPendingPaymentSessions($branchID, $userID = null)
    {
        return self::getSessions($branchID, null, "asc", ["Pending Payment"], null, null, null, null, $userID);
    }

    public static function getTodaySessions($branchID, $userID = null)
    {
        return self::getSessions($branchID, null, "asc", [], date('Y-m-d'), date('Y-m-d'), null, null, $userID);
    }

    public static function getDoneSessions($branchID, $startDate, $endDate, $userID = null)
    {
        return self::getSessions($branchID, null, "desc", ["Done"], $startDate, $endDate, null, null, $userID);
    }

    public static function getSessions($branchID, $roomID = null, $order = 'desc', array $state = [], $startDate = null, $endDate = null, $patient = null, $doctor = null, $openedBy = null, $moneyBy = null, $totalBegin = null, $totalEnd = null, $isCommision = "0", $loadServices = false, $devices_ids = [])
    {
        $rels = ["doctor", "patient", "creator", "accepter", "branch"];
        if ($loadServices)
            array_push($rels, "items.pricelistItem", "items.pricelistItem.device", "items.pricelistItem.area");
        $query = self::with($rels);

        if ($roomID != null && $roomID != 0)
            $query = $query->where("SSHN_ROOM_ID", "=", $roomID);

        if ($branchID != null && $branchID != 0)
            $query = $query->where("SSHN_BRCH_ID", "=", $branchID);

        if (count($state) != 0)
            $query = $query->whereIn("SSHN_STTS", $state);

        if (count($devices_ids) != 0) {
            $query->join('session_items', 'SHIT_SSHN_ID', '=', 'sessions.id')
                ->join('pricelist_items', 'SHIT_PLIT_ID', '=', 'pricelist_items.id')
                ->groupby('sessions.id', 'SSHN_PTNT_ID')->select('sessions.*');
            $query = $query->whereIn("PLIT_DVIC_ID", $devices_ids);
        }

        if ($startDate != null)
            $query = $query->where("SSHN_DATE", ">=", $startDate);

        if ($endDate != null)
            $query = $query->where("SSHN_DATE", "<=", $endDate);

        if ($patient != null && $patient > 0)
            $query = $query->where("SSHN_PTNT_ID", $patient);

        /** @var DashUser */
        $user = Auth::user();
        if ($user->isDoctor() || ($doctor != null && $doctor > 0)) {
            if ($user->isDoctor()) {
                $query = $query->where("SSHN_DCTR_ID", $user->id);
            } else {
                $query = $query->where("SSHN_DCTR_ID", $doctor);
            }
        }

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

    public static function getDoneCount($branchID, $startDate, $endDate, $doctorID = null, $userID = 0)
    {
        $query = self::where("SSHN_DATE", ">=", $startDate)->where("SSHN_DATE", "<=", $endDate)->where("SSHN_STTS", "Done");
        if ($doctorID != null) {
            $query = $query->where("SSHN_DCTR_ID", $doctorID)->where("SSHN_CMSH", 1);
        }
        if ($branchID != 0) {
            $query = $query->where('SSHN_BRCH_ID', $branchID);
        }
        if ($userID != 0) {
            $query = $query->where('SSHN_OPEN_ID', $userID);
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

    public static function getPaidSum($startDate, $endDate, $doctorID = null, $userID = 0)
    {
        $query = self::where("SSHN_DATE", ">=", $startDate)->where("SSHN_DATE", "<=", $endDate)->where("SSHN_STTS", "Done");
        if ($doctorID != null) {
            $query = $query->where("SSHN_DCTR_ID", $doctorID)->where("SSHN_CMSH", 1);
        }
        if ($userID != 0) {
            $query = $query->where('SSHN_OPEN_ID', $userID);
        }
        return ($query->sum("SSHN_PAID") + $query->sum("SSHN_PTNT_BLNC"));
    }

    public static function getTotalSum($startDate, $endDate, $doctorID = null)
    {
        $query = self::where("SSHN_DATE", ">=", $startDate)->where("SSHN_DATE", "<=", $endDate)->where("SSHN_STTS", "Done");
        if ($doctorID != null) {
            $query = $query->where("SSHN_DCTR_ID", $doctorID)->where("SSHN_CMSH", 1);
        }
        $sum = $query->selectRaw("SUM(SSHN_TOTL * ((100-SSHN_DISC)/100)) as totalSum")->first();
        return $sum->totalSum;
    }

    public static function getPendingPaymentCount($branchID = 0, $userID = 0)
    {
        $query = self::where("SSHN_STTS", "Pending Payment");
        if ($branchID != 0) {
            $query = $query->where('SSHN_BRCH_ID', $branchID);
        }
        if ($userID != 0) {
            $query = $query->where('SSHN_OPEN_ID', $userID);
        }
        return $query->count();
    }

    public static function getTodaySessionsCount($branchID = 0, $userID = 0)
    {
        $today = (new DateTime())->format('Y-m-d');
        $query = self::where("SSHN_DATE", "=", $today);
        if ($branchID != 0) {
            $query = $query->where('SSHN_BRCH_ID', $branchID);
        }
        if ($userID != 0) {
            $query = $query->where('SSHN_OPEN_ID', $userID);
        }
        return $query->count();
    }

    public static function getNewCount($branchID = 0, $startDate, $endDate, $userID = 0)
    {
        $query = self::where("SSHN_DATE", ">=", $startDate)->where("SSHN_DATE", "<=", $endDate)->where("SSHN_STTS", "New");
        if ($branchID != 0) {
            $query = $query->where('SSHN_BRCH_ID', $branchID);
        }
        if ($userID != 0) {
            $query = $query->where('SSHN_OPEN_ID', $userID);
        }
        return $query->count();
    }

    public static function getMinTotal()
    {
        return self::min('SSHN_TOTL');
    }

    public static function getMaxTotal()
    {
        return self::max('SSHN_TOTL');
    }

    public static function createNewSession($roomID, $patientID, $doctorID, $date, $startTime, $endTime, $comment = null, $servicesArr = [], $isCommission = false)
    {
        $room = Room::findOrFail($roomID);
        $res = self::insertGetId([
            "SSHN_BRCH_ID"      =>  $room->ROOM_BRCH_ID,
            "SSHN_ROOM_ID"      =>  $room->id,
            "SSHN_DCTR_ID"      =>  $doctorID,
            "SSHN_PTNT_ID"      =>  $patientID,
            "SSHN_DATE"         =>  $date,
            "SSHN_STRT_TIME"    =>  $startTime,
            "SSHN_END_TIME"     =>  $endTime,
            "SSHN_TEXT"         =>  $comment,
            "SSHN_OPEN_ID"      =>  Auth::user()->id,
        ]);
        if ($res) {
            $session = Session::findOrFail($res);
            SendSMSJob::dispatch($session, SmsHandler::MODE_NEW);
            $session->clearServices();

            LaravelLog::debug("Printing services");
            LaravelLog::debug("Array: ");
            LaravelLog::debug(print_r($servicesArr, true));
            foreach ($servicesArr as $serviceObj) {
                LaravelLog::debug(print_r($serviceObj, true));
                $session->addService($serviceObj->priceListID, $serviceObj->unit, $serviceObj->note, false, $serviceObj->isDoctor == "on" ? true : false, false);
            }
            $session->setCommission($isCommission);

            $session->calculateTotal();
            $session->logEvent("Created Session");
            return 1;
        } else return 0;
    }

    ///services
    public function addService($pricelistID, $unit, $note = null, $recalculateTotal = true, $isDoctor = true, $isCollected = 0)
    {
        if ($this->canEditServices())
            DB::transaction(function () use ($pricelistID, $unit, $note, $recalculateTotal, $isDoctor, $isCollected) {
                $pricelistItem = PriceListItem::findOrFail($pricelistID);
                $this->items()->create([
                    "SHIT_PLIT_ID"  =>  $pricelistItem->id,
                    "SHIT_PRCE"     =>  $pricelistItem->PLIT_PRCE,
                    "SHIT_NOTE"     =>  $note,
                    "SHIT_QNTY"     =>  $unit,
                    "SHIT_DCTR"     =>  $isDoctor ? 1 : 0,
                    "SHIT_CLTD_PCKG"     =>  $isCollected,
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
                        if ($packagesToUse < $item->SHIT_QNTY) {
                            //the session will pull client packages less than the packages assigned to the session
                            //we need a new session item to cover the difference
                            $this->addService($item->pricelistItem->id, $item->SHIT_QNTY - $foundPackages, "Added automatically for the uncollected amount after client Package Settlement", false, $item->is_doctor);
                            $item->SHIT_QNTY = $packagesToUse;
                        }
                        $itemsPrice =  $this->patient->usePackage($item->pricelistItem, $packagesToUse, $this->id);
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
                $this->patient->deductBalance($this->SSHN_BRCH_ID, $remainingMoney, $this->id);
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
                    $this->patient->pay($this->SSHN_BRCH_ID, $extra, "Extra Cash Entry from Session#{$this->id}", false);
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
                    Cash::entry($this->SSHN_BRCH_ID, $transTitle, $amount, 0, "Automated Cash Entry for Session#{$this->id}");
                } else {
                    $this->SSHN_PYMT_TYPE = "Visa";
                    $this->save();
                    Visa::entry($this->SSHN_BRCH_ID, $transTitle, $amount, 0, "Automated Visa Entry for Session#{$this->id}");
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

    public function confirmSession()
    {
        $this->SSHN_CONF = 1;
        $this->save();
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
            $this->packageLogs()->delete();
            $this->items()->delete();
            $this->logs()->delete();
            $this->feedback()->delete();

            if ($this->SSHN_PAID > 0)
                if ($this->SSHN_PYMT_TYPE == "Cash") {
                    Cash::entry($this->SSHN_BRCH_ID, "Session#{$this->id} deleted", 0, $this->SSHN_PAID, "Added Automatically after session delete");
                } elseif ($this->SSHN_PYMT_TYPE == "Visa") {
                    Visa::entry($this->SSHN_BRCH_ID, "Session#{$this->id} deleted", 0, $this->SSHN_PAID, "Added Automatically after session delete");
                }

            if ($this->SSHN_PTNT_BLNC > 0)
                $this->patient->pay($this->SSHN_BRCH_ID, $this->SSHN_PTNT_BLNC, "Money Refund after Session delete", false);

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
            
            // Send patient messages for completed session
            SendSMSJob::dispatch($this, 'patient_messages');
        }
    }

    public function createFeedback()
    {
        Feedback::createFeedback($this->SSHN_BRCH_ID, $this->id, $this->SSHN_DATE->add(new DateInterval('P5D'))->format('Y-m-d'));
    }

    public function returnCollectedPackages()
    {
        $this->loadMissing("patient");
        $itemsToReturn = $this->items()->collected()->get();
        foreach ($itemsToReturn as $item) {
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
                SendSMSJob::dispatch($this, SmsHandler::MODE_CANCEL);
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
        return (Auth::user()->canAdmin() || $this->SSHN_STTS == "New");
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

    function branch()
    {
        return $this->belongsTo(Branch::class, "SSHN_BRCH_ID");
    }

    function room()
    {
        return $this->belongsTo(Room::class, "SSHN_ROOM_ID");
    }

    function feedback()
    {
        return $this->hasOne("App\Models\Feedback", "FDBK_SSHN_ID");
    }

    function items()
    {
        return $this->hasMany(SessionItem::class, "SHIT_SSHN_ID");
    }

    function pricelistItems()
    {
        return $this->belongsToMany(PriceListItem::class, "session_items",  "SHIT_SSHN_ID", "SHIT_PLIT_ID");
    }

    function logs()
    {
        return $this->hasMany("App\Models\Log", "LOG_SSHN_ID");
    }

    function packageLogs()
    {
        return $this->hasMany(PackageLog::class, "PKLG_SSHN_ID");
    }

    /**
     * Generate patient messages based on session items matching device/area
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function generatePatientMessages()
    {
        $this->loadMissing('items.pricelistItem.device', 'items.pricelistItem.area');
        
        $matchedMessages = collect();
        $processedCombinations = [];
        
        foreach ($this->items as $sessionItem) {
            $pricelistItem = $sessionItem->pricelistItem;
            
            if (!$pricelistItem) {
                continue;
            }
            
            $deviceID = $pricelistItem->PLIT_DVIC_ID;
            $areaID = $pricelistItem->PLIT_AREA_ID;
            
            // Create a unique key for this device/area combination to avoid duplicates
            $combinationKey = $deviceID . '_' . ($areaID ?? 'null');
            
            if (in_array($combinationKey, $processedCombinations)) {
                continue;
            }
            
            $processedCombinations[] = $combinationKey;
            
            // Find patient messages that match this device and area
            $query = PatientMessage::where('PTMS_DVIC_ID', $deviceID);
            
            // Match messages where:
            // 1. Area ID matches exactly, OR
            // 2. Patient message has no area (null) - meaning it applies to all areas for this device
            if ($areaID) {
                $query->where(function($q) use ($areaID) {
                    $q->where('PTMS_AREA_ID', $areaID)
                      ->orWhereNull('PTMS_AREA_ID');
                });
            } else {
                // If session item has no area, only match messages with no area
                $query->whereNull('PTMS_AREA_ID');
            }
            
            $messages = $query->get();
            $matchedMessages = $matchedMessages->merge($messages);
        }
        
        // Remove duplicates based on message ID
        return $matchedMessages->unique('id');
    }
}
