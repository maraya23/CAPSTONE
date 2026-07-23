<?php

/*
|--------------------------------------------------------------------------
| Mock Board Examination System
|--------------------------------------------------------------------------
| Configuration File
|--------------------------------------------------------------------------
| This file contains constants used throughout the authentication
| module. Changing a value here updates the entire system.
|--------------------------------------------------------------------------
*/

// =========================
// OTP SETTINGS
// =========================

define("OTP_LENGTH", 4);

define("OTP_EXPIRY_MINUTES", 5);

define("OTP_RESEND_SECONDS", 60);

// =========================
// SECURITY SETTINGS
// =========================

define("MAX_OTP_ATTEMPTS", 5);

define("OTP_LOCKOUT_MINUTES", 5);

// =========================
// SESSION SETTINGS
// =========================

define("SESSION_TIMEOUT", 1800); // 30 minutes

?>