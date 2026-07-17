<?php

include "../includes/session.php";
include "../connection.php";

if ($_SESSION['usertype'] != "user") {
    header("Location: ../admin/dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: verify_email.php");
    exit();
}

$id = $_SESSION["ID"];

$enteredOTP = trim($_POST["otp"]);

$sql = "SELECT email,
               pending_email,
               otp,
               otp_expiry
        FROM logintbl
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

// Check if OTP is correct
if ($enteredOTP != $user["otp"]) {

    mysqli_close($conn);

    header("Location: verify_email.php?invalid=1");
    exit();

}

// Check if OTP has expired
if (strtotime($user["otp_expiry"]) < time()) {

    mysqli_close($conn);

    header("Location: verify_email.php?expired=1");
    exit();

}

// Verification successful
$sql = "UPDATE logintbl
        SET email = ?,
            email_verified = 1,
            pending_email = NULL,
            otp = NULL,
            otp_expiry = NULL
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "si",
    $user["pending_email"],
    $id
);

mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);

// Update current session
$_SESSION["email"] = $user["pending_email"];

mysqli_close($conn);

header("Location: profile.php?verified=1");
exit();

?>