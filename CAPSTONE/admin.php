<?php
session_start();

if (!isset($_SESSION['ID'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['usertype'] != "admin") {
    header("Location: user.php");
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

<!-- ===================== SIDEBAR ===================== -->

<td width="220" valign="top">

<h2>ADMIN PANEL</h2>

<hr>

<p>

<a href="admin.php">

<button type="button">

Dashboard

</button>

</a>

</p>

<p>

<a href="admin/manage_users.php">

<button type="button">

Manage Users

</button>

</a>

</p>

<p>

<a href="admin/create_user.php">

<button type="button">

Create User

</button>

</a>

</p>

<p>

<a
href="logout.php"
onclick="return confirm('Are you sure you want to log out?');">

<button type="button">

Logout

</button>

</a>
    
</p>

</td>

<!-- ===================== MAIN CONTENT ===================== -->

<td valign="top">

<h1>Admin Dashboard</h1>

<hr>


<h2>Quick Navigation</h2>

<p>

<a href="admin/manage_users.php">

<button type="button">

Manage User Accounts

</button>

</a>

</p>

<p>

<a href="admin/create_user.php">

<button type="button">

Create New User

</button>

</a>

</p>

</td>

</tr>

</table>

</body>

</html>