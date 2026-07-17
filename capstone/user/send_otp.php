<?php

include "../includes/session.php";
include "../connection.php";
include "../includes/mailer.php";

if ($_SESSION['usertype'] != "user") {
    header("Location: ../admin/dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: connect_email.php");
    exit();
}

$id = $_SESSION["ID"];

$email = trim($_POST["email"]);

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    header("Location: connect_email.php?invalid=1");
    exit();

}

// Check if email is already used
$sql = "SELECT id
        FROM logintbl
        WHERE email = ?
        AND id != ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "si",
    $email,
    $id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    header("Location: connect_email.php?duplicate=1");
    exit();

}

mysqli_stmt_close($stmt);

// Generate 4-digit OTP
$otp = rand(1000, 9999);

// OTP expires in 5 minutes
$expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

// Save pending email and OTP
$sql = "UPDATE logintbl
        SET pending_email = ?,
            otp = ?,
            otp_expiry = ?
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "sssi",
    $email,
    $otp,
    $expiry,
    $id
);

mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);

// Build Email
$subject = "Mock Board Examination - Email Verification";

$body = "

<h2>Mock Board Examination</h2>

<p>Hello!</p>

<p>Your One-Time Password (OTP) is:</p>

<h1>$otp</h1>

<p>This code will expire in <strong>5 minutes</strong>.</p>

<p>If you did not request this verification, you may ignore this email.</p>

";

// Send Email
if(sendMail($email, $subject, $body)){

    mysqli_close($conn);

    header("Location: verify_email.php");
    exit();

}else{

    mysqli_close($conn);

    header("Location: connect_email.php?senderror=1");
    exit();

}

?>