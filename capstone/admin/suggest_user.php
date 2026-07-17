<?php

include "../connection.php";

if (!isset($_POST['firstname'])) {
    exit();
}

$firstname = trim($_POST['firstname']);

// Keep only letters
$firstname = preg_replace("/[^a-zA-Z]/", "", $firstname);

if ($firstname == "") {
    echo json_encode([]);
    exit();
}

$suggestions = [];

while (count($suggestions) < 3) {

    $number = rand(100, 999);

    $username = ucfirst($firstname) . $number;

    $sql = "SELECT ID FROM logintbl WHERE username = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $username);

    mysqli_stmt_execute($stmt);

    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 0) {

        if (!in_array($username, $suggestions)) {
            $suggestions[] = $username;
        }

    }

    mysqli_stmt_close($stmt);
}

echo json_encode($suggestions);

mysqli_close($conn);

?>