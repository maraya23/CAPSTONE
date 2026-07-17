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

if (isset($_POST['create'])) {

    $firstname = trim($_POST['firstname']);
    $middlename = trim($_POST['middlename']);
    $surname = trim($_POST['surname']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
if (
    strlen($password) < 8 ||
    !preg_match('/[A-Za-z]/', $password) ||
    !preg_match('/[0-9]/', $password)
) {
    header("Location: create_user.php?error=password");
    exit();
}
    $usertype = $_POST['usertype'];

    // Check if username already exists
    $checkUsername = "SELECT ID FROM logintbl WHERE username = ?";
    $stmt = mysqli_prepare($conn, $checkUsername);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        die("Username already exists.");
    }

    mysqli_stmt_close($stmt);

    // Check if email already exists
    // Only check duplicate email if an email was entered
if (!empty($email)) {

    $checkEmail = "SELECT ID FROM logintbl WHERE email = ?";

    $stmt = mysqli_prepare($conn, $checkEmail);

    mysqli_stmt_bind_param($stmt, "s", $email);

    mysqli_stmt_execute($stmt);

    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {

        die("Email already exists.");

    }

    mysqli_stmt_close($stmt);

}

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Save the new user
    $sql = "INSERT INTO logintbl
            (firstname, middlename, surname, email, username, password, usertype)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "sssssss",
        $firstname,
        $middlename,
        $surname,
        $email,
        $username,
        $hashedPassword,
        $usertype
    );

    if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    header("Location: manage_users.php?success=1");
    exit();

} else {

    die(
        "<h2>Database Error</h2>" .
        "<strong>Error Number:</strong> " . mysqli_errno($conn) . "<br>" .
        "<strong>Error:</strong> " . mysqli_stmt_error($stmt)
    );

}

} else {

    header("Location: create_user.php");
    exit();

}

?>