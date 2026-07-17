<?php

include "../includes/session.php";
include "../connection.php";

if ($_SESSION['usertype'] != "user") {
    header("Location: ../admin/dashboard.php");
    exit();
}

$id = $_SESSION["ID"];

$sql = "SELECT pending_email, otp_expiry
        FROM logintbl
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

mysqli_close($conn);

if(empty($user["pending_email"])){
    header("Location: profile.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Verify Email</title>

</head>

<body>

<table border="1" width="100%" cellspacing="0" cellpadding="10">

<tr>

<td width="220" valign="top">

<?php include "../includes/user_sidebar.php"; ?>

</td>

<td valign="top">

<h1>Email Verification</h1>

<hr>

<p>

A verification code has been sent to:

<br><br>

<strong>

<?php echo htmlspecialchars($user["pending_email"]); ?>

</strong>

</p>

<?php

if(isset($_GET["invalid"])){

    echo "<p style='color:red;font-weight:bold;'>Incorrect verification code.</p>";

}

if(isset($_GET["expired"])){

    echo "<p style='color:red;font-weight:bold;'>Your verification code has expired.</p>";

}

?>

<form action="verify_otp.php" method="POST">

<p>

Verification Code

<br>

<input
type="text"
name="otp"
maxlength="4"
pattern="[0-9]{4}"
required>

</p>

<button type="submit">

Verify Email

</button>

<a href="connect_email.php">

<button type="button">

Cancel

</button>

</a>

</form>

</td>

</tr>

</table>

</body>

</html>