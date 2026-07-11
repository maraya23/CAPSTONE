<?php

session_start();

require_once __DIR__ . '/../connection.php';

if (!isset($_SESSION['admin_id'])) {

    header('Location: /CAPSTONE/login.php');

    exit();

}

// Create upload folder if it doesn't exist

$uploadDir = __DIR__ . '/../uploads/review_materials/';

if (!is_dir($uploadDir)) {

    mkdir($uploadDir, 0777, true);

}

// Upload file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $title = trim($_POST['title']);

    $category = trim($_POST['category']);

    $fileName = '';

    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {

        $originalName = basename($_FILES['file']['name']);

        $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);

        move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . $fileName);

    }

    $stmt = mysqli_prepare($conn, 'INSERT INTO review_materials(title, category, file_name) VALUES(?,?,?)');

    mysqli_stmt_bind_param($stmt, 'sss', $title, $category, $fileName);

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    header('Location: review_management.php');

    exit();

}

// Delete

if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    $result = mysqli_query($conn, "SELECT file_name FROM review_materials WHERE id=$id");

    if ($row = mysqli_fetch_assoc($result)) {

        $path = $uploadDir . $row['file_name'];

        if (file_exists($path)) unlink($path);

    }

    mysqli_query($conn, "DELETE FROM review_materials WHERE id=$id");

    header('Location: review_management.php');

    exit();

}

// Filters

$search = $_GET['search'] ?? '';

$categoryFilter = $_GET['category'] ?? '';

$sql = "SELECT * FROM review_materials WHERE 1=1";

if ($search != '') {

    $searchEsc = mysqli_real_escape_string($conn, $search);

    $sql .= " AND title LIKE '%$searchEsc%'";

}

if ($categoryFilter != '') {

    $catEsc = mysqli_real_escape_string($conn, $categoryFilter);

    $sql .= " AND category='$catEsc'";

}

$sql .= " ORDER BY id DESC";

$materials = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>

<html>

<head>

<meta charset='UTF-8'>

<meta name='viewport' content='width=device-width, initial-scale=1.0'>

<title>Review Management</title>

<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css'>

<style>

body{margin:0;font-family:Arial;background:#f3f5fb;}

.wrapper{display:flex;min-height:100vh;}

.sidebar{width:280px;background:linear-gradient(180deg,#02152f,#032b69);padding:20px;color:#fff;}

.logo{text-align:center;font-size:28px;font-weight:bold;margin:20px 0 40px;}

.sidebar a{display:block;color:#fff;text-decoration:none;padding:16px;border-radius:14px;margin-bottom:10px;}

.sidebar a.active,.sidebar a:hover{background:#2563eb;}

.main{flex:1;padding:30px;}

.card{background:#fff;border-radius:20px;padding:25px;margin-bottom:25px;box-shadow:0 4px 18px rgba(0,0,0,.08);}

.top{display:flex;justify-content:space-between;align-items:center;gap:15px;flex-wrap:wrap;}

.btn{background:#2563eb;color:#fff;padding:12px 18px;border:none;border-radius:10px;cursor:pointer;text-decoration:none;font-weight:bold;}

.filters{display:grid;grid-template-columns:2fr 1fr auto;gap:15px;margin-top:20px;}

input,select{padding:12px;border:1px solid #ddd;border-radius:10px;width:100%;box-sizing:border-box;}

table{width:100%;border-collapse:collapse;}

th,td{padding:14px;border-bottom:1px solid #eee;text-align:left;}

th{background:#0f2d5c;color:#fff;}

.upload-box{display:none;margin-top:25px;border-top:1px solid #eee;padding-top:25px;}

    .search-box{

    position:relative;

    width:100%;

}

.search-box input{

    width:100%;

    padding-left:40px;

    padding-right:40px;

}

.search-icon{

    position:absolute;

    left:14px;

    top:50%;

    transform:translateY(-50%);

    color:#9ca3af;

}

.clear-btn{

    position:absolute;

    right:10px;

    top:50%;

    transform:translateY(-50%);

    width:24px;

    height:24px;

    border:none;

    background:#e5e7eb;

    border-radius:50%;

    cursor:pointer;

    font-size:16px;

    line-height:24px;

    text-align:center;

    color:#374151;

    display:none;

}

.clear-btn:hover{

    background:#d1d5db;

}
    
@media(max-width:900px){.wrapper{flex-direction:column}.sidebar{width:100%}.filters{grid-template-columns:1fr}}

</style>

</head>

<body>

<div class='wrapper'>

<aside class='sidebar'>

<div class='logo'>e-Nurse Admin</div>

<a href='admin_dashboard.php'><i class='fa-solid fa-table-columns'></i> Dashboard</a>

<a href='manage_users.php'><i class='fa-solid fa-user'></i> User Management</a>

<a href='review_management.php' class='active'><i class='fa-solid fa-file-lines'></i> Review Management</a>

<a href='user_engagement.php'><i class='fa-solid fa-chart-line'></i> User Engagement</a>

<a href='test_bank.php'><i class='fa-solid fa-database'></i> Test Banking</a>

<a href='test_construction.php'><i class='fa-solid fa-pen'></i> Test Construction</a>

<a href='test_administration.php'><i class='fa-solid fa-gear'></i> Test Administration</a>

<a href='reports.php'><i class='fa-solid fa-chart-column'></i> Reports</a>

<a href='../logout.php'><i class='fa-solid fa-right-from-bracket'></i> Logout</a>

</aside>

<main class='main'>

<div class='card'>

<div class='top'>

<div>

<h1 style='margin:0'>Review Management</h1>

<p style='color:#6b7280'>Upload and manage review materials.</p>

</div>

<button class='btn' onclick="document.getElementById('uploadBox').style.display='block'">

<i class='fa-solid fa-cloud-arrow-up'></i> Upload Review Material

</button>

</div>

<form method='GET' class='filters'>

    <div class='search-box'>

        <i class='fa-solid fa-magnifying-glass search-icon'></i>

        <input type='text'

               id='searchInput'

               name='search'

               placeholder='Search materials...'

               value='<?php echo htmlspecialchars($search); ?>'>

        <button type='button'

                class='clear-btn'

                id='clearBtn'

                onclick='clearSearch()'

                title='Clear search'>

            &times;

        </button>

    </div>

    <select name='category'>

        <option value=''>All Categories</option>

        <option value='Fundamentals of Nursing'>Fundamentals of Nursing</option>

        <option value='Medical-Surgical Nursing'>Medical-Surgical Nursing</option>

        <option value='Maternal and Child Nursing'>Maternal and Child Nursing</option>

        <option value='Psychiatric Nursing'>Psychiatric Nursing</option>

        <option value='Community Health Nursing'>Community Health Nursing</option>

    </select>

    <button class='btn' type='submit'>

        <i class='fa-solid fa-filter'></i> Filter

    </button>

</form>

<div id='uploadBox' class='upload-box'>

<form method='POST' enctype='multipart/form-data'>

<input type='text' name='title' placeholder='Material Title' required>

<select name='category' required>

<option value='Fundamentals of Nursing'>Fundamentals of Nursing</option>

<option value='Medical-Surgical Nursing'>Medical-Surgical Nursing</option>

<option value='Maternal and Child Nursing'>Maternal and Child Nursing</option>

<option value='Psychiatric Nursing'>Psychiatric Nursing</option>

<option value='Community Health Nursing'>Community Health Nursing</option>

</select>

<input type='file' name='file' required>

<br><br>

<button class='btn' type='submit'>Save Material</button>

</form>

</div>

</div>

<div class='card'>

<table>

<tr><th>Review Material</th><th>Category</th><th>File</th><th>Date</th><th>Actions</th></tr>

<?php while($row = mysqli_fetch_assoc($materials)) { ?>

<tr>

<td><?php echo htmlspecialchars($row['title']); ?></td>

<td><?php echo htmlspecialchars($row['category']); ?></td>

<td>

    <a href='/CAPSTONE/uploads/review_materials/<?php echo $row['file_name']; ?>' target='_blank'>

        View File

    </a>

</td>

<td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>

<td>

    <a href='?delete=<?php echo $row['id']; ?>' onclick="return confirm('Delete this material?')">

        Delete

    </a>

</td>

</tr>

<?php } ?>

</table>

</div>

</main>

</div>

<script>

const searchInput = document.getElementById('searchInput');

const clearBtn = document.getElementById('clearBtn');

function toggleClearButton() {

    clearBtn.style.display = searchInput.value.trim() !== '' ? 'block' : 'none';

}

function clearSearch() {

    // Reload page without search and category parameters

    window.location.href = 'review_management.php';

}

// Show or hide the X button while typing

searchInput.addEventListener('input', toggleClearButton);

// Run once when the page loads

toggleClearButton();

</script>

</body>

</html>