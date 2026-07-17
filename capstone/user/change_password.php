<?php

include "../includes/session.php";

if ($_SESSION['usertype'] != "user") {
    header("Location: ../admin.php");
    exit();
}

$forceChange = false;

if (isset($_SESSION['must_change_password']) && $_SESSION['must_change_password'] == 1) {
    $forceChange = true;
}

$message = "";
$messageColor = "red";

if (isset($_GET['success'])) {

    $message = "Password changed successfully.";
    $messageColor = "green";

}

if (isset($_GET['error'])) {

    switch($_GET['error']){

        case "current":
            $message = "Current password is incorrect.";
            break;

        case "match":
            $message = "New passwords do not match.";
            break;

        case "same":
            $message = "New password cannot be the same as the current password.";
            break;

        case "format":
            $message = "Password must be at least 8 characters long and contain at least one letter and one number.";
            break;

        case "database":
            $message = "Unable to update password.";
            break;

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Change Password</title>

</head>

<body>

<table border="1" width="100%" cellspacing="0" cellpadding="10">

<tr>

<td width="220" valign="top">

<?php include "../includes/user_sidebar.php"; ?>

</td>

<td valign="top">

<h1>Change Password</h1>

<hr>

<?php if($forceChange){ ?>

<p style="color:red;font-weight:bold;">

You must change your password before you can continue.

</p>

<?php } ?>

<?php

if($message!=""){

echo "<p style='color:$messageColor;font-weight:bold;'>$message</p>";

}

?>

<form action="update_password.php" method="POST">

<p>

Current Password

<br>

<input
type="password"
id="current_password"
name="current_password"
required>

</p>

<p>

New Password

<br>

<input
type="password"
id="new_password"
name="new_password"
required>

</p>

<p>

Confirm New Password

<br>

<input
type="password"
id="confirm_password"
name="confirm_password"
required>

</p>

<p>

<input
type="checkbox"
onclick="togglePassword()">

Show Passwords

</p>

<button type="submit">

Change Password

</button>

<?php if(!$forceChange){ ?>

<a href="profile.php">

<button type="button">

Back to Profile

</button>

</a>

<?php } ?>

</form>

<script>

function togglePassword(){

    let current=document.getElementById("current_password");
    let newpass=document.getElementById("new_password");
    let confirm=document.getElementById("confirm_password");

    if(current.type==="password"){

        current.type="text";
        newpass.type="text";
        confirm.type="text";

    }else{

        current.type="password";
        newpass.type="password";
        confirm.type="password";

    }

}

</script>

</td>

</tr>

</table>

</body>

</html>