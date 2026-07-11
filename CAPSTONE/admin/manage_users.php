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
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Users - e-Nurse</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#f3f5fb;
}


.wrapper{
    display:flex;
    min-height:100vh;
}


/* SIDEBAR */

.sidebar{

    width:280px;

    background:linear-gradient(180deg,#02152f,#032b69);

    padding:20px;

    color:white;

}


.logo{

    font-size:28px;

    font-weight:bold;

    text-align:center;

    margin:20px 0 40px;

}


.menu a{

    display:block;

    color:white;

    text-decoration:none;

    padding:16px 18px;

    border-radius:14px;

    margin-bottom:10px;

    font-size:16px;

}


.menu a:hover,
.menu a.active{

    background:#2563eb;

}



/* MAIN */

.main{

    flex:1;

    padding:30px;

    height:100vh;

    overflow:hidden;

    box-sizing:border-box;

}



.topbar{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:25px;

}


.topbar h1{

    margin:0;

    font-size:36px;

    color:#111827;

}



.welcome{

    background:white;

    padding:14px 22px;

    border-radius:16px;

    box-shadow:0 3px 12px rgba(0,0,0,.08);

}



/* CONTENT */

.card{

    background:white;

    padding:25px;

    border-radius:20px;

    box-shadow:0 4px 15px rgba(0,0,0,.08);

}



.search-box{

    margin-bottom:20px;

}


.search-box input{

    padding:12px;

    width:300px;

    border-radius:10px;

    border:1px solid #ccc;

}



button{

    background:#2563eb;

    color:white;

    border:none;

    padding:12px 20px;

    border-radius:10px;

    cursor:pointer;

}


button:hover{

    background:#1d4ed8;

}



table{

    width:100%;

    border-collapse:collapse;

}



th{

    background:#2563eb;

    color:white;

    padding:12px;

}



td{

    padding:10px;

    border-bottom:1px solid #ddd;

}



td input{

    width:95%;

    padding:8px;

    border-radius:8px;

    border:1px solid #ccc;

}



.delete{

    background:#dc2626;

}


.delete:hover{

    background:#b91c1c;

}


.success{

    color:green;

    font-weight:bold;

}


.error{

    color:red;

    font-weight:bold;

}


.info{

    color:#2563eb;

    font-weight:bold;

}
    
    .button-group{

    display:flex;

    gap:10px;

    align-items:center;

}


.add-user{

    background:#16a34a;

}


.add-user:hover{

    background:#15803d;

}

</style>

</head>


<body>


<div class="wrapper">


<aside class="sidebar">


<div class="logo">
e-Nurse
</div>


<nav class="menu">


<a href="admin_dashboard.php">

<i class="fa-solid fa-table-columns"></i>
Dashboard

</a>


<a href="manage_users.php" class="active">

<i class="fa-solid fa-user"></i>
User Management

</a>


<a href="review_management.php">

<i class="fa-solid fa-file-lines"></i>
Review Management

</a>


<a href="user_engagement.php">

<i class="fa-solid fa-chart-line"></i>
User Engagement

</a>


<a href="test_bank.php">

<i class="fa-solid fa-database"></i>
Test Banking

</a>


<a href="test_construction.php">

<i class="fa-solid fa-pen"></i>
Test Construction

</a>


<a href="test_administration.php">

<i class="fa-solid fa-gear"></i>
Test Administration

</a>


<a href="reports.php">

<i class="fa-solid fa-chart-column"></i>
Reports

</a>


<a href="../logout.php">

<i class="fa-solid fa-right-from-bracket"></i>
Logout

</a>


</nav>


</aside>



<main class="main">


<div class="topbar">

<h1>Manage User Accounts</h1>


<div class="welcome">

<i class="fa-solid fa-user"></i>

Welcome, Admin

</div>


</div>



<div class="card">

<?php

if(isset($_GET['success'])){
echo "<p class='success'>Changes saved successfully.</p>";
}

if(isset($_GET['nochanges'])){
echo "<p class='info'>No changes were made.</p>";
}

if(isset($_GET['deleted'])){
echo "<p class='success'>User account deleted successfully.</p>";
}

if(isset($_GET['error'])){
echo "<p class='error'>Unable to process request.</p>";
}

?>



<form method="GET" class="search-box">

<input
type="text"
name="search"
placeholder="Search user..."
value="<?php echo htmlspecialchars($search); ?>">


<button type="submit">

<i class="fa-solid fa-search"></i>
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



<form action="update_user.php" method="POST">


<div class="button-group">

<button type="submit">

<i class="fa-solid fa-save"></i>
Save Changes

</button>


<a href="create_user.php">

<button type="button" class="add-user">

<i class="fa-solid fa-user-plus"></i>
Add User

</button>

</a>

</div>


<br><br>


<table>


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


<a href="delete_user.php?id=<?php echo $row['id']; ?>"
onclick="return confirm('Delete this account?');">


<button type="button" class="delete">

<i class="fa-solid fa-trash"></i>

</button>


</a>


<?php }else{ ?>


<button type="button" disabled>

<i class="fa-solid fa-lock"></i>

</button>


<?php } ?>


</td>


</tr>


<?php } ?>


</table>


</form>


</div>


</main>


</div>


</body>

</html>