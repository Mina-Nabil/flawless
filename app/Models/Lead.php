<?php

namespace App\Models;

use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Lead extends Model
{
    use HasFactory;

    const STTS_NEW = 'New';
    const STTS_INTERESTED = 'Interested';
    const STTS_NOT_INTERESTED = 'Not-interested';
    const STTS_NO_ANSWER = 'No-Answer';
    const STTS_PATIENT = 'Patient';

    const STATUSES = [
        self::STTS_NEW,
        self::STTS_INTERESTED,
        self::STTS_NOT_INTERESTED,
        self::STTS_NO_ANSWER,
        self::STTS_PATIENT
    ];

    protected $fillable = [
        'LEAD_NAME',
        'LEAD_MOBN',
        'LEAD_STTS',
        'LEAD_USER_ID',
        'LEAD_ADRS',
        'LEAD_NOTE',
        'LEAD_PRMO'
    ];

    ///static functions
    public static function importLeads($file) {

        $spreadsheet = IOFactory::load($file);
        if (!$spreadsheet) {
            throw new Exception('Failed to read files content');
        }
        $activeSheet = $spreadsheet->getActiveSheet();
        $highestRow = $activeSheet->getHighestDataRow();

        for ($i = 2; $i <= $highestRow; $i++) {
            $id     =  $activeSheet->getCell('A' . $i)->getValue();
            $name     =  $activeSheet->getCell('B' . $i)->getValue();
            $mob      =  $activeSheet->getCell('C' . $i)->getValue();
            $address  =  $activeSheet->getCell('D' . $i)->getValue();
            $promo  =  $activeSheet->getCell('E' . $i)->getValue();
            $note   =  $activeSheet->getCell('F' . $i)->getValue();
            $username     =  $activeSheet->getCell('G' . $i)->getValue();
          

            if (!$name || !$mob) continue;

            $user = DashUser::where('DASH_USNM', strtolower($username))->first();
            if (!$user) $user = Auth::user();

            if ($id) {
                /** @var self */
                $lead = self::find($id);
                if (!$lead) continue;
                $lead->editLead($name, $mob, $promo, $address, $note, $user->id );
          
            } else {

                $lead = self::newLead($user->id, $name, $mob, $promo, $address, $note);
            }
        }

        return true;
    }

    public static function downloadTemplate()
    {

        $leads = self::with('user')->get();

        $template = IOFactory::load(resource_path('imports/nady_import.xlsx'));
        if (!$template) {
            throw new Exception('Failed to read template file');
        }
        $newFile = $template->copy();


        $writer = new Xlsx($newFile);
        $file_path = "leads_export_template.xlsx";
        $public_file_path = storage_path($file_path);
        $writer->save($public_file_path);

        return response()->download($public_file_path)->deleteFileAfterSend(true);
    }

    public static function exportLeads()
    {

        $leads = self::with('user')->get();

        $template = IOFactory::load(resource_path('imports/nady_import.xlsx'));
        if (!$template) {
            throw new Exception('Failed to read template file');
        }
        $newFile = $template->copy();
        $activeSheet = $newFile->getActiveSheet();

        $i = 2;
        foreach ($leads as $lead) {
            $activeSheet->getCell('A' . $i)->setValue($lead->id);
            $activeSheet->getCell('B' . $i)->setValue($lead->LEAD_NAME);
            $activeSheet->getCell('C' . $i)->setValue($lead->LEAD_MOBN);
            $activeSheet->getCell('D' . $i)->setValue($lead->LEAD_ADRS);
            $activeSheet->getCell('E' . $i)->setValue($lead->LEAD_PRMO);
            $activeSheet->getCell('F' . $i)->setValue($lead->LEAD_NOTE);
            $activeSheet->getCell('G' . $i)->setValue($lead->user?->DASH_USNM);
            $i++;
        }

        $writer = new Xlsx($newFile);
        $file_path = "leads_export.xlsx";
        $public_file_path = storage_path($file_path);
        $writer->save($public_file_path);

        return response()->download($public_file_path)->deleteFileAfterSend(true);
    }


    public static function newLead($user_id, $name, $mob, $promo = null, $adrs = null, $note = null)
    {
        $lead = new self();
        $lead->LEAD_USER_ID = $user_id;
        $lead->LEAD_NAME = $name;
        $lead->LEAD_ADRS = $adrs;
        $lead->LEAD_MOBN = $mob;
        $lead->LEAD_STTS = self::STTS_NEW;
        $lead->LEAD_NOTE = $note;
        $lead->LEAD_PRMO = $promo;
        $lead->save();
        return $lead;
    }

    public static function getCountCreatedThisMonth()
    {
        $startOfMonth = (new DateTime('now'))->format('Y-m-01');
        $endOfMonth = (new DateTime('now'))->format('Y-m-t');
        return DB::table('leads')->whereBetween("created_at", [$startOfMonth, $endOfMonth])->count();
    }


    ///model functions
    public function createPatient()
    {
        $patient = new Patient();
        $patient->PTNT_NAME = $this->LEAD_NAME;
        $patient->PTNT_ADRS = $this->LEAD_ADRS;
        $patient->PTNT_MOBN = $this->LEAD_MOBN;
        $patient->PTNT_BLNC =  0;
        $patient->PTNT_PRLS_ID = (PriceList::getDefaultList()->id ?? NULL);
        $patient->PTNT_NOTE = $this->LEAD_NOTE;
        $patient->save();
        $this->followups()->update([
            "FLUP_PTNT_ID"  => $patient->id
        ]);
        $this->LEAD_STTS = self::STTS_PATIENT;
        $this->LEAD_PTNT_ID = $patient->id;
        $this->save();
        return $patient;
    }

    public function addFollowup(Carbon $date, $note = null)
    {
        return FollowUp::createFollowup(1, null, $date->format('Y-m-d'), $note, $this->id);
    }

    public function setStatus($status)
    {
        $this->LEAD_STTS = $status;
        $this->save();
    }

    public function editLead($name, $mob, $promo = null, $adrs = null, $note = null, $user_id = null)
    {

        $this->LEAD_NAME = $name;
        $this->LEAD_ADRS = $adrs;
        $this->LEAD_MOBN = $mob;
        $this->LEAD_STTS = self::STTS_NEW;
        $this->LEAD_NOTE = $note;
        $this->LEAD_PRMO = $promo;
        $this->LEAD_USER_ID = $user_id;
        return $this->save();
    }

    public function deleteLead()
    {
        $this->followups()->forceDelete();
        return $this->delete();
    }

    ///scopes
    public function scopeByUser($query)
    {
        /** @var DashUser */
        $user = Auth::user();
        if ($user->isOwner()) return $query;

        return $query->where('LEAD_USER_ID', $user->id);
    }

    ///relations
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'LEAD_PTNT_ID');
    }

    public function user()
    {
        return $this->belongsTo(DashUser::class, 'LEAD_USER_ID');
    }

    public function followups()
    {
        return $this->hasMany(FollowUp::class, 'FLUP_LEAD_ID');
    }
}
