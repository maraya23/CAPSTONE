<?php

include "../includes/session.php";
include "../connection.php";

if ($_SESSION['usertype'] != "user") {
    header("Location: ../admin/dashboard.php");
    exit();
}

$id = $_SESSION['ID'];

$sql = "SELECT email, email_verified
        FROM logintbl
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Connect Email</title>

</head>

<body>

<table border="1" width="100%" cellspacing="0" cellpadding="10">

<tr>

<td width="220" valign="top">

<?php include "../includes/user_sidebar.php"; ?>

</td>

<td valign="top">

<h1>Connect Email</h1>

<hr>

<?php

if(isset($_GET['duplicate'])){
    echo "<p style='color:red;font-weight:bold;'>Email is already being used by another account.</p>";
}

if(isset($_GET['invalid'])){
    echo "<p style='color:red;font-weight:bold;'>Please enter a valid email address.</p>";
}

if(isset($_GET['senderror'])){
    echo "<p style='color:red;font-weight:bold;'>Unable to send verification code.</p>";
}

?>

<?php if($user['email_verified'] == 1){ ?>

<p style="color:green;font-weight:bold;">

Your email has already been verified.

</p>

<p>

<strong>Email:</strong>

<?php echo htmlspecialchars($user['email']); ?>

</p>

<a href="profile.php">

<button type="button">

Back to Profile

</button>

</a>

<?php } else { ?>

<form action="send_otp.php" method="POST">

<p>

Email Address

<br>

<input
type="email"
name="email"
required>

</p>

<button type="submit">

Send Verification Code

</button>

<a href="profile.php">

<button type="button">

Cancel

</button>

</a>

</form>

<?php } ?>

</td>

</tr>

</table>

</body>

</html>