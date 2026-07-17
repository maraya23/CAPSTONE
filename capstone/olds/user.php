<?php

session_start();

include "connection.php";

if (!isset($_SESSION['ID'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['usertype'] != "user") {
    header("Location: admin.php");
    exit();
}

$id = $_SESSION['ID'];

$sql = "SELECT firstname, middlename, surname, username, email
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

<title>User Dashboard</title>

</head>

<body>

<table border="1" width="100%" cellspacing="0" cellpadding="10">

<tr>

<!-- ================= SIDEBAR ================= -->

<td width="220" valign="top">

<h2>USER PANEL</h2>

<hr>

<p>

<a href="user.php">

<button type="button">

My Profile

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

<!-- ================= MAIN CONTENT ================= -->

<td valign="top">

<h1>My Profile</h1>

<hr>

<p>

<strong>First Name:</strong>

<?php echo htmlspecialchars($user['firstname']); ?>

</p>

<p>

<strong>Middle Name:</strong>

<?php echo htmlspecialchars($user['middlename']); ?>

</p>

<p>

<strong>Surname:</strong>

<?php echo htmlspecialchars($user['surname']); ?>

</p>

<p>

<strong>Username:</strong>

<?php echo htmlspecialchars($user['username']); ?>

</p>

<p>

<strong>Email:</strong>

<?php echo htmlspecialchars($user['email']); ?>

</p>

</td>

</tr>

</table>

</body>

</html>