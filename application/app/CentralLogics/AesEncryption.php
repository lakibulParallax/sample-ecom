<?php

namespace App\CentralLogics;

class AesEncryption
{
    public static function encrypt($input, $key) {
        return base64_encode(openssl_encrypt($input, "AES-256-ECB", $key, OPENSSL_RAW_DATA));
    }

    public static function decrypt($input, $key) {
        return openssl_decrypt(base64_decode($input), "AES-256-ECB", $key, OPENSSL_RAW_DATA);
    }

}
