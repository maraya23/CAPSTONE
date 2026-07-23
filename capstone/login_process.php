<?php

/*
|--------------------------------------------------------------------------
| Mock Board Examination System
|--------------------------------------------------------------------------
| Authentication Module
|--------------------------------------------------------------------------
| File: login_process.php
| Version: 2.1
|--------------------------------------------------------------------------
*/

session_start();

require_once "connection.php";
require_once "includes/config.php";
require_once "includes/flash.php";
require_once "includes/auth_helper.php";
require_once "includes/mailer.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: login.php");
    exit();

}

$username = trim($_POST["username"]);
$password = $_POST["password"];

/*
|--------------------------------------------------------------------------
| Get User
|--------------------------------------------------------------------------
*/

$sql = "SELECT
            ID,
            firstname,
            middlename,
            surname,
            username,
            password,
            email,
            email_verified,
            usertype,
            must_change_password,
            two_factor_enabled,
            otp_attempts,
            otp_locked_until
        FROM logintbl
        WHERE username = ?";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {

    setFlash("error", "Database error.");

    header("Location: login.php");
    exit();

}

mysqli_stmt_bind_param($stmt, "s", $username);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) != 1) {

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    setFlash("error", "Invalid username or password.");

    header("Location: login.php");
    exit();

}

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

/*
|--------------------------------------------------------------------------
| Verify Password
|--------------------------------------------------------------------------
*/

if (!password_verify($password, $user["password"])) {

    mysqli_close($conn);

    setFlash("error", "Invalid username or password.");

    header("Location: login.php");
    exit();

}

/*
|--------------------------------------------------------------------------
| Check OTP Lock
|--------------------------------------------------------------------------
*/

if (!empty($user["otp_locked_until"])) {

    if (strtotime($user["otp_locked_until"]) > time()) {

        mysqli_close($conn);

        $_SESSION["lockout_until"] = $user["otp_locked_until"];

        header("Location: locked.php");
        exit();

    } else {

        /*
        |--------------------------------------------------------------------------
        | Lock Expired
        | Automatically unlock the account
        |--------------------------------------------------------------------------
        */

        $sql = "UPDATE logintbl
                SET
                    otp_attempts = 0,
                    otp_locked_until = NULL
                WHERE ID = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $user["ID"]
        );

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        $user["otp_attempts"] = 0;
        $user["otp_locked_until"] = NULL;

    }

}

/*
|--------------------------------------------------------------------------
| Two-Factor Authentication
|--------------------------------------------------------------------------
*/

if ($user["two_factor_enabled"] == 1 && $user["email_verified"] == 1) {

    $otp = generateOTP();

    $expiry = generateOTPExpiry();

    $sql = "UPDATE logintbl
            SET
                login_otp = ?,
                login_otp_expiry = ?,
                otp_attempts = 0,
                otp_locked_until = NULL
            WHERE ID = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ssi",
        $otp,
        $expiry,
        $user["ID"]
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    $subject = "Mock Board Examination Login Verification";

    $body = "
    <h2>Login Verification</h2>

    <p>Hello <strong>{$user['firstname']}</strong>,</p>

    <p>Your verification code is:</p>

    <h1>{$otp}</h1>

    <p>This code will expire in <strong>" . OTP_EXPIRY_MINUTES . " minutes</strong>.</p>

    <p>If you did not attempt to log in, you may safely ignore this email.</p>
    ";

    if (!sendMail($user["email"], $subject, $body)) {

        mysqli_close($conn);

        setFlash("error", "Unable to send verification code.");

        header("Location: login.php");
        exit();

    }

    $_SESSION["login_user_id"] = $user["ID"];

    mysqli_close($conn);

    header("Location: verify_login_otp.php");
    exit();

}

/*
|--------------------------------------------------------------------------
| Normal Login
|--------------------------------------------------------------------------
*/

session_regenerate_id(true);

$_SESSION["ID"] = $user["ID"];
$_SESSION["firstname"] = $user["firstname"];
$_SESSION["middlename"] = $user["middlename"];
$_SESSION["surname"] = $user["surname"];
$_SESSION["username"] = $user["username"];
$_SESSION["email"] = $user["email"];
$_SESSION["usertype"] = $user["usertype"];
$_SESSION["must_change_password"] = $user["must_change_password"];

mysqli_close($conn);

if ($user["usertype"] == "admin") {

    header("Location: admin/dashboard.php");
    exit();

}

if ($user["must_change_password"] == 1) {

    header("Location: user/change_password.php");
    exit();

}

header("Location: user/dashboard.php");
exit();

?>