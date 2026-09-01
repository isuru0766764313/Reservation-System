<?php
namespace App\Http\Controllers;

use App\Models\Services\ESMSWS;
use Illuminate\Support\Facades\Log;

class SmsServiceController extends Controller
{
    private $alias;
    private $message;
    private $recipients;
    private $username;
    private $password;

    //public function __construct();

    public function __construct($message,$recipients)
    {
        $this->alias = config('services.sms.mobile_host_alias');
        $this->message = $message;
        // $this->recipients = (array)$recipients;
        $this->recipients = $recipients;

        $this->username = config('services.sms.mobile_host_username');
        $this->password = config('services.sms.mobile_host_password');
    }

    public function sendSms()
    {
        $sms = new ESMSWS();
        $session = $sms->createSession('',$this->username,$this->password,''); 
        
        /* Send message using class properties
        $result = $sms->sendMessages(
            $session,
            $this->alias,
            $this->message,
            $this->recipients,
            0
        );*/
        
        $sms->sendMessages($session, $this->alias, $this->message,explode(',',$this->recipients), 0);
        $sms->closeSession($session);
        //return $result;
    }

    /*public function sendSms()
    {
        try
        {
        $sms = new ESMSWS();
        // $session = $sms->createSession('', $this->username, $this->password, '');
        // $result = $sms->sendMessages($session, $this->alias, $this->message, $this->recipients, 0);

        $session = $sms->createSession('', 'esmsusr_1pf1', 'vkd7mn', '');
        $result = $sms->sendMessages($session, 'PMs OFFICE', 'Test message 2', explode('94773543173,94716963607'), 0);
        $sms->closeSession($session);
        return $result;
        }
        catch (\Exception $e)
        {
        Log::error('SMS sending failed: ' . $e->getMessage());
        return false;
        }
    }*/
}