<?php


namespace App\Helpers;

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
        if (!env('SMS_EG_ACTIVE', false)) return false;

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
            $msg = urlencode("Thanks for choosing us.
            Unfortunately, the appointment scheduled on {$session->carbon_date->rawFormat('D, d/m')} at {$session->carbon_date->format('H:i A')} is cancelled.
            Please contact us for more details.
            01270002080");
        } else {
            $doctorName = $session->doctor == null ? false : $session->doctor->DASH_FLNM;
            $msg = urlencode("Hi {$session->patient->first_name}, your session is on {$session->carbon_date->rawFormat('D, d/m')} at {$session->carbon_date->format('h:i A')} at {$session->branch->BRCH_NAME} branch" . ($doctorName ? " with Dr. {$doctorName} " : '') .  ". See you! Flawless clinics\n{$session->branch->BRCH_LOCT}");
        }

        $API_USER = env('SMS_EG_USERNAME');
        $API_KEY = env('SMS_EG_PASSWORD');
        $API_SENDER = env('SMS_SENDER_TOKEN');
        $API_ENV = env('APP_ENV') === 'production' ? 1 : 2;
        try {
            $response = Http::post("https://smsmisr.com/api/SMS/?environment={$API_ENV}&username={$API_USER}&password={$API_KEY}&language=1&sender={$API_SENDER}&mobile={$session->patient->sms_mobile_number}&message={$msg}");
            Log::info("-------------- SENDING SMS -------------");
            Log::info('Phone ' . $session->patient->sms_mobile_number);
            Log::info('Content: ' . $msg);
            Log::info(print_r($response->json(), true));
            Log::info("-------------- -------------- -------------");

            return $response->json()['code'] == '1901';
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }
}
