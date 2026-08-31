<?php


namespace App\Helpers;

use App\Models\PatientMessage;
use App\Models\Session;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsHandler
{

    const MODE_NEW = 'new';
    const MODE_UPDATE = 'update';
    const MODE_CANCEL = 'cancel';


    public static function sendSessionMessage(Session $session, string $mode): bool
    {
        if (!config('services.smseg.active', false)) return false;

        $session->loadMissing('patient', 'branch', 'doctor');
        $action = 'confirmed';
        switch ($mode) {
            case self::MODE_UPDATE:
                $action = 'updated';
                break;

            case self::MODE_CANCEL:
                $action = 'cancelled';
                break;

            default:
            case self::MODE_NEW:
                $action = 'confirmed';

                break;
        }
        if ($action == 'cancelled') {
            $message = "Thanks for choosing us.
            Unfortunately, the appointment scheduled on {$session->carbon_date->rawFormat('D, d/m')} at {$session->carbon_date->format('H:i A')} is cancelled.
            Please contact us for more details.
            01270002080";
        } else {
            $doctorName = $session->doctor == null ? false : $session->doctor->DASH_FLNM;
            $message = "Hi {$session->patient->first_name}, your session is on {$session->carbon_date->rawFormat('D, d/m')} at {$session->carbon_date->format('h:i A')} at {$session->branch->BRCH_NAME} branch" . ($doctorName ? " with Dr. {$doctorName} " : '') .  ". See you! Flawless clinics\n{$session->branch->BRCH_LOCT}";
        }

        try {
            Log::info("-------------- SENDING SMS -------------");
            Log::info('Phone ' . $session->patient->sms_mobile_number);
            Log::info('Content: ' . $message);

            $success = self::sendSms($session->patient->sms_mobile_number, $message);

            Log::info("-------------- -------------- -------------");

            return $success;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    /**
     * Send patient messages for a completed session
     * 
     * @param Session $session
     * @return void
     */
    public static function sendPatientMessages(Session $session): void
    {
        Log::info("PM JOB STARTED");

        if (!config('services.smseg.pm_active', false)) return;

        Log::info("SMS EG PM ACTIVE: " . config('services.smseg.pm_active', false));

        $session->load('patient');
        
        // Get patient messages that match the session items
        $patientMessages = $session->generatePatientMessages();

        Log::info("-------------- PATIENT MESSAGES -------------");
        Log::info(print_r($patientMessages->toArray(), true));
        Log::info("-------------- -------------- -------------");
        
        if ($patientMessages->isEmpty()) {
            return;
        }

        $mobile = $session->patient->sms_mobile_number;

        foreach ($patientMessages as $patientMessage) {
            try {
                $messageText = $patientMessage->getMessageForSession($session);
                
                Log::info("-------------- SENDING PATIENT MESSAGE SMS -------------");
                Log::info('Session ID: ' . $session->id);
                Log::info('Patient Message ID: ' . $patientMessage->id);
                Log::info('Phone: ' . $mobile);
                Log::info('Content: ' . $messageText);

                self::sendSms($mobile, $messageText);
                
                Log::info("-------------- -------------- -------------");

            } catch (Exception $e) {
                Log::error("Failed to send patient message SMS for session {$session->id}, message {$patientMessage->id}: " . $e->getMessage());
                report($e);
            }
        }
    }

    private static function sendSms(string $mobile, string $message): bool
    {
        $username = config('services.smseg.username');
        $password = config('services.smseg.password');
        $sendername = config('services.smseg.sender');

        if (empty($username) || empty($password)) {
            Log::error('SMSeg credentials missing. Set SMS_EG_USERNAME and SMS_EG_PASSWORD in .env');
            return false;
        }

        $response = Http::acceptJson()->get('https://plus.smssmartegypt.com/api/PlusSMS/SendSMS', [
            'username'   => $username,
            'password'   => $password,
            'sendername' => $sendername,
            'message'    => $message,
            'mobiles'    => $mobile,
        ]);

        Log::info(print_r($response->json(), true));

        return ($response->json()['type'] ?? null) === 'success';
    }
}
