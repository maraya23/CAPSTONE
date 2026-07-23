<?php

include "../includes/session.php";
include "../connection.php";

if ($_SESSION['usertype'] != "user") {
    header("Location: ../admin/dashboard.php");
    exit();
}

$id = $_SESSION['ID'];

$sql = "SELECT firstname,
               middlename,
               surname,
               username,
               email,
               email_verified,
               two_factor_enabled
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

<title>My Profile</title>

</head>

<body>

<table border="1" width="100%" cellspacing="0" cellpadding="10">

<tr>

<td width="220" valign="top">

<?php include "../includes/user_sidebar.php"; ?>

</td>

<td valign="top">

<h1>My Profile</h1>

<hr>

<?php

if(isset($_GET['verified'])){
    echo "<p style='color:green;font-weight:bold;'>Email verified successfully.</p>";
}

if(isset($_GET['twofa'])){
    echo "<p style='color:green;font-weight:bold;'>Two-Factor Authentication settings updated.</p>";
}

?>

<p>

<strong>First Name</strong><br>

<input
type="text"
value="<?php echo htmlspecialchars($user['firstname']); ?>"
readonly>

</p>

<p>

<strong>Middle Name</strong><br>

<input
type="text"
value="<?php echo htmlspecialchars($user['middlename']); ?>"
readonly>

</p>

<p>

<strong>Surname</strong><br>

<input
type="text"
value="<?php echo htmlspecialchars($user['surname']); ?>"
readonly>

</p>

<p>

<strong>Username</strong><br>

<input
type="text"
value="<?php echo htmlspecialchars($user['username']); ?>"
readonly>

</p>

<p>

<strong>Email</strong><br>

<input
type="text"
value="<?php echo htmlspecialchars($user['email']); ?>"
readonly>

</p>

<p>

<strong>Email Status</strong><br>

<?php

if($user['email_verified']==1){

    echo "<span style='color:green;font-weight:bold;'>✔ Verified</span>";

}else{

    echo "<span style='color:red;font-weight:bold;'>✖ Not Verified</span>";

}

?>

</p>

<br>

<?php if($user['email_verified']==0){ ?>

<a href="connect_email.php">

<button type="button">

Connect Email

</button>

</a>

<?php } ?>

<a href="change_password.php">

<button type="button">

Change Password

</button>

</a>

<hr>

<h2>Two-Factor Authentication (2FA)</h2>

<p>

<strong>Status:</strong>

<?php

if($user['two_factor_enabled']){

    echo "<span style='color:green;font-weight:bold;'>Enabled</span>";

}else{

    echo "<span style='color:red;font-weight:bold;'>Disabled</span>";

}

?>

</p>

<?php

if($user['email_verified']==1){

?>

<p>

Every time you log in, a 4-digit verification code will be sent to your verified email.

</p>

<form action="toggle_2fa.php" method="POST">

<button type="submit">

<?php

if($user['two_factor_enabled']){

    echo "Disable Two-Factor Authentication";

}else{

    echo "Enable Two-Factor Authentication";

}

?>

</button>

</form>

<?php

}else{

?>

<p style="color:red;">

Verify your email first before enabling Two-Factor Authentication.

</p>

<?php

}

?>

</td>

</tr>

</table>

</body>

</html>