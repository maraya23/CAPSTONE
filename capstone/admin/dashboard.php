<?php

include "../includes/session.php";

if ($_SESSION['usertype'] != "admin") {
    header("Location: ../user/dashboard.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Admin Dashboard</title>

</head>

<body>

<table border="1" width="100%" cellspacing="0" cellpadding="10">

<tr>

<td width="220" valign="top">

<?php include "../includes/admin_sidebar.php"; ?>

</td>

<td valign="top">

<h1>Admin Dashboard</h1>

<hr>

<p>

Welcome,

<strong>

<?php echo htmlspecialchars($_SESSION['firstname']); ?>

</strong>

</p>

<p>

Username:

<?php echo htmlspecialchars($_SESSION['username']); ?>

</p>

<p>

Role:

<?php echo htmlspecialchars($_SESSION['usertype']); ?>

</p>

</td>

</tr>

</table>

</body>

</html>