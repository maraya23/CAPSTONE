<?php

include "../includes/session.php";
include "../connection.php";

if ($_SESSION['usertype'] != "user") {
    header("Location: ../admin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: change_password.php");
    exit();
}

$currentPassword = $_POST["current_password"];
$newPassword = $_POST["new_password"];
$confirmPassword = $_POST["confirm_password"];

$id = $_SESSION["ID"];

// Get current password from database
$sql = "SELECT password, must_change_password
        FROM logintbl
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

// Verify current password
if (!password_verify($currentPassword, $user["password"])) {

    header("Location: change_password.php?error=current");
    exit();

}

// Check if new passwords match
if ($newPassword != $confirmPassword) {

    header("Location: change_password.php?error=match");
    exit();

}

// Prevent same password
if (password_verify($newPassword, $user["password"])) {

    header("Location: change_password.php?error=same");
    exit();

}

// Password validation
if (
    strlen($newPassword) < 8 ||
    !preg_match('/[A-Za-z]/', $newPassword) ||
    !preg_match('/[0-9]/', $newPassword)
) {

    header("Location: change_password.php?error=format");
    exit();

}

// Hash new password
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Update password and remove first-login requirement
$sql = "UPDATE logintbl
        SET password = ?,
            must_change_password = 0
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "si",
    $hashedPassword,
    $id
);

if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    mysqli_close($conn);

    // Update session
    $_SESSION["must_change_password"] = 0;

    // If this was the first login,
    // send user to dashboard
    if ($user["must_change_password"] == 1) {

        header("Location: dashboard.php");

    } else {

        header("Location: change_password.php?success=1");

    }

    exit();

}

mysqli_stmt_close($stmt);

mysqli_close($conn);

header("Location: change_password.php?error=database");
exit();

?>