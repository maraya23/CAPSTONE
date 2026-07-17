<?php

session_start();

if (!isset($_SESSION['ID'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['usertype'] != "admin") {
    header("Location: ../user.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Create User Account</title>

<style>

#usernameSuggestions{

    width:250px;

    border:1px solid #ccc;

    display:none;

}

.suggestion{

    padding:8px;

    cursor:pointer;

    border-bottom:1px solid #ddd;

}

.suggestion:hover{

    background:#efefef;

}

</style>

</head>

<body>

<table border="1" width="100%" cellspacing="0" cellpadding="10">

<tr>

<!-- ===================== SIDEBAR ===================== -->

<td width="220" valign="top">

<?php include "../includes/admin_sidebar.php"; ?>

</td>

<!-- ===================== MAIN CONTENT ===================== -->

<td valign="top">

<h2>Create User Account</h2>

<?php
if (isset($_GET['error']) && $_GET['error'] == "password") {
    echo "<p style='color:red;'>
    Password must be at least 8 characters long and contain at least one letter and one number.
    </p>";
}

if (isset($_GET['error']) && $_GET['error'] == "username") {
    echo "<p style='color:red;'>Username already exists.</p>";
}

if (isset($_GET['error']) && $_GET['error'] == "email") {
    echo "<p style='color:red;'>Email already exists.</p>";
}
?>

<form action="save_user.php" method="POST">

<label>First Name</label><br>

<input
type="text"
id="firstname"
name="firstname"
required>

<br><br>

<label>Middle Name</label><br>

<input
type="text"
name="middlename">

<br><br>

<label>Surname</label><br>

<input
type="text"
name="surname"
required>

<br><br>

<label>Email</label><br>

<input
type="email"
name="email">

<br><br>

<label>Username</label><br>

<input
type="text"
id="username"
name="username"
autocomplete="off"
required>

<div id="usernameSuggestions"></div>

<br>

<label>Password</label><br>

<input
type="password"
name="password"
required>

<br><br>

<label>User Type</label><br>

<select name="usertype" required>

<option value="">Select User Type</option>

<option value="admin">Admin</option>

<option value="user">User</option>

</select>

<br><br>

<button type="submit" name="create">

Create User

</button>

</form>

</td>

</tr>

</table>

<script>

const firstname=document.getElementById("firstname");

const username=document.getElementById("username");

const suggestions=document.getElementById("usernameSuggestions");

let timer;

firstname.addEventListener("input",function(){

    clearTimeout(timer);

    timer=setTimeout(loadSuggestions,400);

});

function loadSuggestions(){

    let name=firstname.value.trim();

    if(name===""){

        suggestions.innerHTML="";

        suggestions.style.display="none";

        return;

    }

    fetch("suggest_username.php",{

        method:"POST",

        headers:{

            "Content-Type":"application/x-www-form-urlencoded"

        },

        body:"firstname="+encodeURIComponent(name)

    })

    .then(response=>response.json())

    .then(data=>{

        suggestions.innerHTML="";

        if(data.length===0){

            suggestions.style.display="none";

            return;

        }

        suggestions.style.display="block";

        data.forEach(function(item){

            let div=document.createElement("div");

            div.className="suggestion";

            div.textContent=item;

            div.onclick=function(){

                username.value=item;

                suggestions.style.display="none";

            };

            suggestions.appendChild(div);

        });

    });

}

</script>

</body>

</html>