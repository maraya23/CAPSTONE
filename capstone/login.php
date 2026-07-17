<?php
session_start();
require_once "connection.php";

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $sql = "SELECT ID, firstname, middlename, surname, username, password, email, usertype
            FROM logintbl
            WHERE username = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {

        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) == 1) {

            $row = mysqli_fetch_assoc($result);

            if (password_verify($password, $row["password"])) {

                session_regenerate_id(true);

                $_SESSION["ID"] = $row["ID"];
                $_SESSION["firstname"] = $row["firstname"];
                $_SESSION["middlename"] = $row["middlename"];
                $_SESSION["surname"] = $row["surname"];
                $_SESSION["username"] = $row["username"];
                $_SESSION["email"] = $row["email"];
                $_SESSION["usertype"] = $row["usertype"];
                $_SESSION["must_change_password"] = $row["must_change_password"];

                if ($row["usertype"] === "admin") {

                    header("Location: admin/dashboard.php");
                    exit();

                } else {

                    if ($row["must_change_password"] == 1) {

                        header("Location: user/change_password.php");
                        exit();

                    } else {

                        header("Location: user/dashboard.php");
                        exit();

                    }

                }

            } else {
                $error_message = "Invalid Username or Password.";
            }

        } else {
            $error_message = "Invalid Username or Password.";
        }

        mysqli_stmt_close($stmt);

    } else {

        $error_message = "Database Error.";

    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login</title>

<!--<link rel="stylesheet" href="style.css">-->

</head>

<body>

<div class="container">

<div class="form-box">

<form action="" method="POST">

<h1>Log in</h1>

<?php
if (!empty($error_message)) {
    echo "<p style='color:red;'>$error_message</p>";
}
?>

<input
    type="text"
    name="username"
    placeholder="Username"
    required>

<input
    type="password"
    id="password"
    name="password"
    placeholder="Password"
    required>

<label>
    <input
        type="checkbox"
        id="showPassword">
    Show Password
</label>

<br><br>

<button type="submit">
    Log in
</button>

</form>

</div>

</div>

<script>

const password = document.getElementById("password");
const showPassword = document.getElementById("showPassword");

showPassword.addEventListener("change", function () {

    if (this.checked) {
        password.type = "text";
    } else {
        password.type = "password";
    }

});

</script>

</body>

</html>