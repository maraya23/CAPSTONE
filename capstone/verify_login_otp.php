<?php

session_start();

require_once "connection.php";
require_once "includes/flash.php";
require_once "includes/auth_helper.php";

if (!isset($_SESSION["login_user_id"])) {

    header("Location: login.php");
    exit();

}

$userID = $_SESSION["login_user_id"];

$sql = "SELECT email
        FROM logintbl
        WHERE ID = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $userID);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

mysqli_close($conn);

$maskedEmail = maskEmail($user["email"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Two-Factor Authentication</title>

</head>

<body>

<div class="container">

<div class="form-box">

<h1>Two-Factor Authentication</h1>

<p>

A 4-digit verification code has been sent to:

<br><br>

<strong>

<?php echo htmlspecialchars($maskedEmail); ?>

</strong>

</p>

<?php

showFlash();

?>

<form action="verify_login_process.php" method="POST">

<input
type="text"
name="otp1"
id="otp1"
maxlength="1"
inputmode="numeric"
autocomplete="off"
required>

<input
type="text"
name="otp2"
id="otp2"
maxlength="1"
inputmode="numeric"
autocomplete="off"
required>

<input
type="text"
name="otp3"
id="otp3"
maxlength="1"
inputmode="numeric"
autocomplete="off"
required>

<input
type="text"
name="otp4"
id="otp4"
maxlength="1"
inputmode="numeric"
autocomplete="off"
required>

<br><br>

<button type="submit">

Verify Login

</button>

</form>

<br>

<form action="resend_login_otp.php" method="POST">

<button type="submit">

Resend Code

</button>

</form>

<br>

<a href="login.php">

Cancel Login

</a>

</div>

</div>

<script>

const inputs = document.querySelectorAll("input[maxlength='1']");

inputs.forEach((input, index) => {

    input.addEventListener("input", function () {

        this.value = this.value.replace(/\D/g, "");

        if (this.value !== "" && index < inputs.length - 1) {

            inputs[index + 1].focus();

        }

    });

    input.addEventListener("keydown", function (e) {

        if (e.key === "Backspace" && this.value === "" && index > 0) {

            inputs[index - 1].focus();

        }

    });

});

inputs[0].addEventListener("paste", function (e) {

    e.preventDefault();

    let paste = (e.clipboardData || window.clipboardData)
        .getData("text")
        .replace(/\D/g, "")
        .substring(0, 4);

    for (let i = 0; i < paste.length; i++) {

        inputs[i].value = paste[i];

    }

    if (paste.length === 4) {

        inputs[3].focus();

    }

});

</script>

</body>

</html>