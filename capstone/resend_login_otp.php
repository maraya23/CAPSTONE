<?php

session_start();

require_once "connection.php";
require_once "includes/mailer.php";

if (!isset($_SESSION["login_user_id"])) {

    header("Location: login.php");
    exit();

}

$userID = $_SESSION["login_user_id"];

// Get user information

$sql = "SELECT firstname,
               email,
               email_verified
        FROM logintbl
        WHERE ID = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $userID);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$user || $user["email_verified"] == 0) {

    mysqli_close($conn);

    header("Location: login.php");
    exit();

}

// Generate new OTP

$otp = str_pad(rand(0, 9999), 4, "0", STR_PAD_LEFT);

$expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

// Save new OTP

$sql = "UPDATE logintbl
        SET
            login_otp = ?,
            login_otp_expiry = ?
        WHERE ID = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ssi",
    $otp,
    $expiry,
    $userID
);

mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);

// Send email

$subject = "Mock Board Examination Login Verification";

$body = "
<h2>Login Verification</h2>

<p>Hello <strong>{$user['firstname']}</strong>,</p>

<p>Your new verification code is:</p>

<h1>{$otp}</h1>

<p>This code expires in <strong>5 minutes</strong>.</p>

<p>If you did not request this code, please ignore this email.</p>
";

if (!sendMail($user["email"], $subject, $body)) {

    mysqli_close($conn);

    header("Location: login.php?error=email");
    exit();

}

mysqli_close($conn);

header("Location: verify_login_otp.php");
exit();

?>