<?php

namespace App\CPU;

class sms_module
{
    public static function send($receiver, $otp)
    {
        return self::infobip($receiver, $otp);
    }

    public static function infobip($receiver, $otp)
    {
        $config = self::get_settings('infobip');
        if (isset($config) && $config['status'] == 1) {
            $url = $config['url'];

            $body = '{"messages":[{"destinations":[{"to":"';
            $body .= $receiver;
            $body .= '"}],"from":"';
            $body .= $config['name'];
            $body .= '","text":"This is a sample message ';
            $body .= $otp;
            $body .= '"}]}';

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==',
                    'Content-Type: application/json',
                    'Accept: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            return $response;
        }

    }
}
