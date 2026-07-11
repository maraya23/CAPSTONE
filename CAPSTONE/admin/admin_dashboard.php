<?php

error_reporting(E_ALL);

ini_set('display_errors', 1);

session_start();

require_once __DIR__ . '/../connection.php';

$totalUsers = 0;

$check = mysqli_query($conn, "SHOW TABLES LIKE 'users'");

if ($check && mysqli_num_rows($check) > 0) {

    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");

    if ($result) {

        $row = mysqli_fetch_assoc($result);

        $totalUsers = $row['total'];

    }

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset='UTF-8'>

<meta name='viewport' content='width=device-width, initial-scale=1.0'>

<title>e-Nurse Admin Dashboard</title>

<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css'>

<style>

body{margin:0;font-family:Arial,sans-serif;background:#f3f5fb;}

.wrapper{display:flex;min-height:100vh;}

.sidebar{width:280px;background:linear-gradient(180deg,#02152f,#032b69);padding:20px;color:#fff;}

.logo{font-size:28px;font-weight:bold;text-align:center;margin:20px 0 40px;}

.menu a{display:block;color:#fff;text-decoration:none;padding:16px 18px;border-radius:14px;margin-bottom:10px;}

.menu a.active,.menu a:hover{background:#2563eb;}

.main{flex:1;padding:30px;}

.topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;}

.topbar h1{font-size:48px;color:#111827;margin:0;}

.welcome{background:#fff;padding:18px 24px;border-radius:18px;box-shadow:0 2px 10px rgba(0,0,0,.06);}

.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:24px;margin-bottom:35px;}

.card{background:#fff;padding:30px;border-radius:22px;text-align:center;box-shadow:0 4px 18px rgba(0,0,0,.08);}

.icon{width:88px;height:88px;border-radius:18px;margin:0 auto 20px;display:flex;align-items:center;justify-content:center;font-size:38px;}

.blue{background:#e8f0ff;color:#2563eb;}

.green{background:#e8f7ea;color:#2f9e44;}

.yellow{background:#fff6db;color:#d4a000;}

.purple{background:#f1e9ff;color:#7c3aed;}

.card h3{color:#4b5563;margin-bottom:20px;}

.num{font-size:68px;font-weight:bold;}

.panel{background:#fff;padding:35px;border-radius:22px;box-shadow:0 4px 18px rgba(0,0,0,.08);}

.panel h2{margin-top:0;margin-bottom:25px;font-size:34px;}

.actions{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:24px;}

.btn{display:flex;align-items:center;justify-content:center;gap:10px;text-decoration:none;color:#fff;padding:28px 18px;border-radius:18px;font-weight:bold;font-size:18px;}

.btn-blue{background:#2563eb;}

.btn-green{background:#34a853;}

.btn-orange{background:#f97316;}

.btn-purple{background:#7c3aed;}

</style>

</head>

<body>

<div class='wrapper'>

<aside class='sidebar'>

<div class='logo'>e-Nurse</div>

<nav class='menu'>

<a href='admin_dashboard.php' class='active'><i class='fa-solid fa-table-columns'></i> Dashboard</a>

<a href='manage_users.php'><i class='fa-solid fa-user'></i> User Management</a>

<a href='/CAPSTONE/admin/review_management.php'>

    <i class='fa-solid fa-file-lines'></i> Review Management

</a>

<a href='user_engagement.php'><i class='fa-solid fa-chart-line'></i> User Engagement</a>

<a href='test_bank.php'><i class='fa-solid fa-database'></i> Test Banking</a>

<a href='test_construction.php'><i class='fa-solid fa-pen'></i> Test Construction</a>

<a href='test_administration.php'><i class='fa-solid fa-gear'></i> Test Administration</a>

<a href='reports.php'><i class='fa-solid fa-chart-column'></i> Reports</a>

<a href='../logout.php'><i class='fa-solid fa-right-from-bracket'></i> Logout</a>

</nav>

</aside>

<main class='main'>

<div class='topbar'>

<h1>Admin Dashboard</h1>

<div class='welcome'><i class='fa-solid fa-user'></i> Welcome, Admin</div>

</div>

<div class='cards'>

<div class='card'><div class='icon blue'><i class='fa-solid fa-users'></i></div><h3>Total Users</h3><div class='num' style='color:#2563eb;'><?php echo $totalUsers; ?></div></div>

<div class='card'><div class='icon green'><i class='fa-solid fa-file-lines'></i></div><h3>Review Materials</h3><div class='num' style='color:#2f9e44;'>0</div></div>

<div class='card'><div class='icon yellow'><i class='fa-solid fa-circle-question'></i></div><h3>Total Questions</h3><div class='num' style='color:#d4a000;'>0</div></div>

<div class='card'><div class='icon purple'><i class='fa-solid fa-clipboard-check'></i></div><h3>Mock Exams</h3><div class='num' style='color:#7c3aed;'>0</div></div>

</div>

<div class='panel'>

<h2>Quick Actions</h2>

<div class='actions'>

<?php // CREATE USER BUTTON ?>
<a href='create_user.php' class='btn btn-blue'><i class='fa-solid fa-user-plus'></i> Create User</a>

<a href='manage_users.php' class='btn btn-green'><i class='fa-solid fa-users'></i> Manage Users</a>

<a href='upload_material.php' class='btn btn-orange'><i class='fa-solid fa-cloud-arrow-up'></i> Upload Review Material</a>

<a href='create_mock_exam.php' class='btn btn-purple'><i class='fa-solid fa-clipboard-list'></i> Create Mock Exam</a>

</div>

</div>

</main>

</div>

</body>

</html>