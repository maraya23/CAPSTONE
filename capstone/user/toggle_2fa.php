<?php

include "../includes/session.php";
include "../connection.php";

if ($_SESSION['usertype'] != "user") {
    header("Location: ../admin/dashboard.php");
    exit();
}

$id = $_SESSION["ID"];

// Get current email verification and 2FA status
$sql = "SELECT email_verified, two_factor_enabled
        FROM logintbl
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

// Prevent enabling 2FA without a verified email
if ($user["email_verified"] == 0) {

    mysqli_close($conn);

    header("Location: profile.php");
    exit();

}

// Toggle 2FA   
$newStatus = ($user["two_factor_enabled"] == 1) ? 0 : 1;

$sql = "UPDATE logintbl
        SET two_factor_enabled = ?
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $newStatus,
    $id
);

mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);

mysqli_close($conn);

header("Location: profile.php?twofa=1");
exit();

?>