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
     * Replaces {patient} placeholder with the patient's name
     * 
     * @param Session $session
     * @return string
     */
    public function getMessageForSession($session)
    {
        $session->loadMissing('patient');
        $patientName = $session->patient->PTNT_NAME ?? 'Patient';
        
        // Replace {patient} placeholder with actual patient name
        $message = str_replace('{patient}', $patientName, $this->PTMS_MSSG);
        
        return $message;
    }
}
