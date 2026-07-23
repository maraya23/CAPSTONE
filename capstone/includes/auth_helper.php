<?php

/*
|--------------------------------------------------------------------------
| Mock Board Examination System
|--------------------------------------------------------------------------
| Authentication Helper
|--------------------------------------------------------------------------
| Reusable authentication functions.
|--------------------------------------------------------------------------
*/

require_once __DIR__ . "/config.php";

/**
 * Generate a numeric OTP.
 */
function generateOTP()
{
    return str_pad(
        rand(0, pow(10, OTP_LENGTH) - 1),
        OTP_LENGTH,
        "0",
        STR_PAD_LEFT
    );
}

/**
 * Generate OTP expiry timestamp.
 */
function generateOTPExpiry()
{
    return date(
        "Y-m-d H:i:s",
        strtotime("+" . OTP_EXPIRY_MINUTES . " minutes")
    );
}

/**
 * Mask an email address.
 *
 * Example:
 * john@gmail.com
 * becomes
 * jo**@gmail.com
 */
function maskEmail($email)
{
    if (empty($email)) {
        return "";
    }

    $parts = explode("@", $email);

    $name = $parts[0];

    $domain = $parts[1];

    if (strlen($name) <= 2) {

        $masked = substr($name, 0, 1) . "*";

    } else {

        $masked =
            substr($name, 0, 2) .
            str_repeat("*", strlen($name) - 2);

    }

    return $masked . "@" . $domain;
}

?>