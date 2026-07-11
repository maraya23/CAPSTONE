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

if (!isset($_POST['firstname'])) {
    header("Location: manage_users.php");
    exit();
}

$changesMade = false;

foreach ($_POST['firstname'] as $id => $firstname) {

    $firstname = trim($firstname);
    $middlename = trim($_POST['middlename'][$id]);
    $surname = trim($_POST['surname'][$id]);
    $email = trim($_POST['email'][$id]);

    // ==========================
    // Get current record
    // ==========================

    $sql = "SELECT firstname, middlename, surname, email
            FROM logintbl
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "i", $id);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $current = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    // ==========================
    // Check duplicate email
    // Blank email is allowed
    // ==========================

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

            header("Location: manage_users.php?duplicate=email");
            exit();

        }

        mysqli_stmt_close($stmt);

    }

    // ==========================
    // Check if anything changed
    // ==========================

    if (
        $firstname != $current['firstname'] ||
        $middlename != $current['middlename'] ||
        $surname != $current['surname'] ||
        $email != $current['email']
    ) {

        $changesMade = true;

        $sql = "UPDATE logintbl
                SET
                    firstname = ?,
                    middlename = ?,
                    surname = ?,
                    email = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ssssi",
            $firstname,
            $middlename,
            $surname,
            $email,
            $id
        );

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

    }

}

mysqli_close($conn);

if ($changesMade) {

    header("Location: manage_users.php?success=1");

} else {

    header("Location: manage_users.php?nochanges=1");

}

exit();

?>