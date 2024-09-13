<?php

namespace App\Helper;

class ApiHelper {

    public static function jsonResponse($message, $status, $code, $data){
        $response = [
            'message'  => $message,
            'status'   => $status,
            'code'     => $code,
            'data'     => $data
        ];
        return response()->json($response, $code);
    }

    public static function generateSlug($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public static function generateUniqueSlug($text, $counter = 0)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        // Append counter if it's greater than 0
        if ($counter > 0) {
            $text .= '-' . $counter;
        }

        return $text;
    }

    public static function generateRandomPassword() {
        // Generate a random 8-digit number
        $password = mt_rand(10000000, 99999999);
        return $password;
    }
}
