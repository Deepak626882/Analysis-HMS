<?php

namespace App\Helpers;

use App\Models\EnviroWhatsapp;
use App\Models\WhatsappLog;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappSend
{
    protected $propertyid;
    protected $isAllowedToSend = true;

    public function __construct()
    {
        $this->propertyid = Auth::user()?->propertyid;
        $wpenv = EnviroWhatsapp::where('propertyid', $this->propertyid)->first();

        if ($wpenv && $wpenv->whatsappbal <= 10) {
            $this->isAllowedToSend = false;
            WhatsappLog::create([
                'propertyid' => $this->propertyid,
                'type' => 'Balance Error',
                'recipient_phone_number' => '',
                'template_id' => '',
                'parameters' => '',
                'response' => "Only $wpenv->whatsappbal Left. Please Recharge First.",
                'http_code' => 500,
                'status' => 'failed',
                'u_name' => Auth::user()?->name,
            ]);
        }
    }

    public function MuzzTech($msgdata, $phone, $type, $templatecolumn)
    {
        if (!$this->isAllowedToSend) {
            return false;
        }
        // Format date/time values
        foreach ($msgdata as &$value) {
            $time = strtotime($value);
            if ($time) {
                if (date('Y-m-d', $time) === $value) {
                    $value = date('d-M-Y', $time);
                } elseif (date('H:i:s', $time) === $value || date('H:i', $time) === $value) {
                    $value = date('H:i', $time);
                }
            }
        }
        unset($value);

        // Split phone numbers by comma and trim spaces
        $phoneNumbers = array_map('trim', explode(',', $phone));

        $wpenv = EnviroWhatsapp::where('propertyid', $this->propertyid)->first();
        $bearercode = $wpenv->bearercode;
        $templateid = $wpenv->{$templatecolumn};
        $url = rtrim($wpenv->whatsappurl, '/');
        $variablecount = count($msgdata);

        $values = $msgdata;
        $parameters = [];

        for ($i = 0; $i < $variablecount; $i++) {
            $parameters[] = ['text' => $values[$i] ?? ''];
        }

        Log::info('Preparing Msg Data Whatsapp Send: ' . json_encode($parameters));

        foreach ($phoneNumbers as $recipientPhone) {

            $payload = json_encode([
                "template_id" => $templateid,
                "media_url" => "",
                "parameters" => $parameters,
                "country_code" => $wpenv->pphonenoprefix,
                "recipient_phone_number" => $recipientPhone,
            ]);

            Log::info('Prefix: ' . $wpenv->pphonenoprefix . ' Phone Numbers: ' . $recipientPhone . ', type: ' . $type);

            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $bearercode,
                'Content-Type: application/json',
                'Accept: application/json'
            ]);

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            WhatsappLog::create([
                'propertyid' => $this->propertyid,
                'type' => $type,
                'recipient_phone_number' => $recipientPhone,
                'template_id' => $templateid,
                'parameters' => json_encode($parameters),
                'response' => $response,
                'http_code' => $httpcode,
                'status' => $httpcode == 200 ? 'success' : 'failed',
                'u_name' => Auth::user()->name,
            ]);

            if ($httpcode == 200) {
                ResHelper::updataincdnc('enviro_whatsapp', 'increment', 'whatsappsend');
                ResHelper::updataincdnc('enviro_whatsapp', 'decrement', 'whatsappbal');
            }
        }

        return true;
    }
}
