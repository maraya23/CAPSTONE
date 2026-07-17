<?php

include "../includes/session.php";
include "../connection.php";

if ($_SESSION['usertype'] != "user") {
    header("Location: ../admin/dashboard.php");
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

if(isset($_GET['success'])){
    echo "<p style='color:green;font-weight:bold;'>Changes saved successfully.</p>";
}

if(isset($_GET['nochanges'])){
    echo "<p style='color:blue;font-weight:bold;'>No changes were made.</p>";
}

if(isset($_GET['duplicate'])){
    echo "<p style='color:red;font-weight:bold;'>Email already exists.</p>";
}

?>

<form action="update_profile.php" method="POST">

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
type="email"
name="email"
value="<?php echo htmlspecialchars($user['email']); ?>">

</p>

<br>

<button type="submit">

Save Changes

</button>

<a href="change_password.php">

<button type="button">

Change Password

</button>

</a>

</form>

</td>

</tr>

</table>

</body>

</html>