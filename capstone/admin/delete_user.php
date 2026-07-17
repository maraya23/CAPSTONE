<?php

session_start();

include "../connection.php";

if (!isset($_SESSION['ID'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['usertype'] != "admin") {
    header("Location: ../user.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$id = (int)$_GET['id'];

// Prevent admin from deleting their own account
if ($id == $_SESSION['ID']) {
    header("Location: manage_users.php?error=selfdelete");
    exit();
}

// Check if the user exists
$sql = "SELECT id FROM logintbl WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    header("Location: manage_users.php?error=notfound");
    exit();
}

mysqli_stmt_close($stmt);

// Delete the user
$sql = "DELETE FROM logintbl WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    header("Location: manage_users.php?deleted=1");
    exit();

} else {

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    header("Location: manage_users.php?error=delete");
    exit();

}

?>