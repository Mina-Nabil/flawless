<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientMessage extends Model
{
    protected $table = "patient_messages";
    public $timestamps = false;

    public $fillable = ["PTMS_DVIC_ID", "PTMS_AREA_ID", "PTMS_MSSG"];

    public function device(){
        return $this->belongsTo(Device::class, 'PTMS_DVIC_ID');
    }

    public function area(){
        return $this->belongsTo(Area::class, 'PTMS_AREA_ID');
    }

    /**
     * Get the formatted message text for a given session
     * Replaces {patient} placeholder with the patient's first name only
     * Converts \r\n to actual newlines
     * 
     * @param Session $session
     * @return string
     */
    public function getMessageForSession($session)
    {
        $session->loadMissing('patient');
        $fullName = $session->patient->PTNT_NAME ?? 'Patient';
        
        // Get first name only (first word)
        $nameParts = explode(' ', $fullName);
        $patientName = $nameParts[0] ?? 'Patient';
        
        // Replace {patient} placeholder with patient's first name
        $message = str_replace('{patient}', $patientName, $this->PTMS_MSSG);
        
        // Convert \r\n to actual newlines
        $message = str_replace('\r\n', "\r\n", $message);
        $message = str_replace('\n', "\n", $message);
        $message = str_replace('\r', "\r", $message);
        
        return $message;
    }
}
