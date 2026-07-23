<?php

session_start();

require_once "connection.php";
require_once "includes/config.php";
require_once "includes/flash.php";

if (!isset($_SESSION["login_user_id"])) {

    header("Location: login.php");
    exit();

}

if ($_SERVER["REQUEST_METHOD"] != "POST") {

    header("Location: verify_login_otp.php");
    exit();

}

$userID = $_SESSION["login_user_id"];

/*
|--------------------------------------------------------------------------
| Combine the 4 OTP boxes
|--------------------------------------------------------------------------
*/

$otp =
    trim($_POST["otp1"]) .
    trim($_POST["otp2"]) .
    trim($_POST["otp3"]) .
    trim($_POST["otp4"]);

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
            email,
            password,
            usertype,
            must_change_password,
            login_otp,
            login_otp_expiry,
            otp_attempts,
            otp_locked_until
        FROM logintbl
        WHERE ID = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $userID);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$user) {

    session_destroy();

    mysqli_close($conn);

    header("Location: login.php");
    exit();

}

/*
|--------------------------------------------------------------------------
| Check Lockout
|--------------------------------------------------------------------------
*/

if (!empty($user["otp_locked_until"])) {

    if (strtotime($user["otp_locked_until"]) > time()) {

        mysqli_close($conn);

        $_SESSION["lockout_until"] = $user["otp_locked_until"];

        header("Location: locked.php");
        exit();

    }

}

/*
|--------------------------------------------------------------------------
| OTP Expired
|--------------------------------------------------------------------------
*/

if (strtotime($user["login_otp_expiry"]) < time()) {

    mysqli_close($conn);

    setFlash("error", "Verification code has expired.");

    header("Location: verify_login_otp.php");
    exit();

}

/*
|--------------------------------------------------------------------------
| Incorrect OTP
|--------------------------------------------------------------------------
*/

if ($otp != $user["login_otp"]) {

    $attempts = $user["otp_attempts"] + 1;

    if ($attempts >= MAX_OTP_ATTEMPTS) {

        $lockedUntil = date(
            "Y-m-d H:i:s",
            strtotime("+" . OTP_LOCKOUT_MINUTES . " minutes")
        );

        $sql = "UPDATE logintbl
                SET
                    otp_attempts = ?,
                    otp_locked_until = ?
                WHERE ID = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "isi",
            $attempts,
            $lockedUntil,
            $userID
        );

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        mysqli_close($conn);

        $_SESSION["lockout_until"] = $lockedUntil;

        header("Location: locked.php");
        exit();

    }

    $sql = "UPDATE logintbl
            SET otp_attempts = ?
            WHERE ID = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $attempts,
        $userID
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    $remaining = MAX_OTP_ATTEMPTS - $attempts;

    mysqli_close($conn);

    setFlash(
        "error",
        "Invalid verification code. Remaining attempts: " . $remaining
    );

    header("Location: verify_login_otp.php");
    exit();

}

/*
|--------------------------------------------------------------------------
| Successful Verification
|--------------------------------------------------------------------------
*/

$sql = "UPDATE logintbl
        SET
            login_otp = NULL,
            login_otp_expiry = NULL,
            otp_attempts = 0,
            otp_locked_until = NULL
        WHERE ID = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $userID);

mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);

/*
|--------------------------------------------------------------------------
| Remove Temporary Session
|--------------------------------------------------------------------------
*/

unset($_SESSION["login_user_id"]);
unset($_SESSION["lockout_until"]);

/*
|--------------------------------------------------------------------------
| Create Login Session
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

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

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