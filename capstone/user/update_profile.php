<?php

include "../includes/session.php";
include "../connection.php";

if ($_SESSION['usertype'] != "user") {
    header("Location: ../admin/dashboard.php");
    exit();
}

$id = $_SESSION['ID'];

$email = trim($_POST['email']);

// Get current email
$sql = "SELECT email
        FROM logintbl
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$current = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

// No changes made
if ($email == $current['email']) {

    mysqli_close($conn);

    header("Location: profile.php?nochanges=1");

    exit();

}

// Check duplicate email (only if not blank)
if (!empty($email)) {

    $sql = "SELECT id
            FROM logintbl
            WHERE email = ?
            AND id <> ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "si", $email, $id);

    mysqli_stmt_execute($stmt);

    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {

        mysqli_stmt_close($stmt);

        mysqli_close($conn);

        header("Location: profile.php?duplicate=1");

        exit();

    }

    mysqli_stmt_close($stmt);

}

// Update email
$sql = "UPDATE logintbl
        SET email = ?
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "si",
    $email,
    $id
);

mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);

// Update session
$_SESSION['email'] = $email;

mysqli_close($conn);

header("Location: profile.php?success=1");

exit();

?>