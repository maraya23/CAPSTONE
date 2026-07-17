<?php

error_reporting(0);
ini_set('display_errors', 0);

session_start();

include "../connection.php";

if (!isset($_SESSION['ID'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['usertype'] != "admin") {
    header("Location: ../user.php");
    exit();
}

$search = "";

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

if ($search != "") {

    $sql = "SELECT * FROM logintbl
            WHERE firstname LIKE ?
            OR middlename LIKE ?
            OR surname LIKE ?
            OR username LIKE ?
            OR email LIKE ?
            ORDER BY id ASC";

    $stmt = mysqli_prepare($conn, $sql);

    $keyword = "%".$search."%";

    mysqli_stmt_bind_param(
        $stmt,
        "sssss",
        $keyword,
        $keyword,
        $keyword,
        $keyword,
        $keyword
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

} else {

    $sql = "SELECT * FROM logintbl ORDER BY id ASC";

    $result = mysqli_query($conn,$sql);

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Manage User Accounts</title>

</head>

<body>

<table border="1" width="100%" cellspacing="0" cellpadding="10">

<tr>

<td width="220" valign="top">

<?php include "../includes/admin_sidebar.php"; ?>

</td>

<td valign="top">

<h1>Manage User Accounts</h1>

<?php

if(isset($_GET['success'])){
    echo "<p style='color:green;font-weight:bold;'>Changes saved successfully.</p>";
}

if(isset($_GET['nochanges'])){
    echo "<p style='color:blue;font-weight:bold;'>No changes were made.</p>";
}

if(isset($_GET['deleted'])){
    echo "<p style='color:green;font-weight:bold;'>User account deleted successfully.</p>";
}

if(isset($_GET['error']) && $_GET['error']=="selfdelete"){
    echo "<p style='color:red;font-weight:bold;'>You cannot delete your own account.</p>";
}

if(isset($_GET['error']) && $_GET['error']=="delete"){
    echo "<p style='color:red;font-weight:bold;'>Unable to delete user account.</p>";
}

if(isset($_GET['error']) && $_GET['error']=="notfound"){
    echo "<p style='color:red;font-weight:bold;'>User account not found.</p>";
}

?>

<form method="GET">

<input
type="text"
name="search"
placeholder="Search..."
value="<?php echo htmlspecialchars($search); ?>">

<button type="submit">

Search

</button>

<?php if($search!=""){ ?>

<a href="manage_users.php">

<button type="button">

Clear

</button>

</a>

<?php } ?>

</form>

<br>

<form action="update_user.php" method="POST">

<button type="submit">

Save Changes

</button>

<br><br>

<table border="1" cellpadding="10">

<tr>

<th>First Name</th>
<th>Middle Name</th>
<th>Surname</th>
<th>Username</th>
<th>Email</th>
<th>User Type</th>
<th>Action</th>

</tr>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<td>

<input
type="text"
name="firstname[<?php echo $row['id']; ?>]"
value="<?php echo htmlspecialchars($row['firstname']); ?>">

</td>

<td>

<input
type="text"
name="middlename[<?php echo $row['id']; ?>]"
value="<?php echo htmlspecialchars($row['middlename']); ?>">

</td>

<td>

<input
type="text"
name="surname[<?php echo $row['id']; ?>]"
value="<?php echo htmlspecialchars($row['surname']); ?>">

</td>

<td>

<?php echo htmlspecialchars($row['username']); ?>

</td>

<td>

<input
type="email"
name="email[<?php echo $row['id']; ?>]"
value="<?php echo htmlspecialchars($row['email']); ?>">

</td>

<td>

<?php echo htmlspecialchars($row['usertype']); ?>

</td>

<td>

<?php if($row['id'] != $_SESSION['ID']){ ?>

<a
href="delete_user.php?id=<?php echo $row['id']; ?>"
onclick="return confirm('Are you sure you want to delete this account?');">

<button type="button">

Delete

</button>

</a>

<?php }else{ ?>

<button
type="button"
disabled>

Delete

</button>

<?php } ?>

</td>

</tr>

<?php } ?>

</table>

</form>

</td>

</tr>

</table>

</body>

</html>