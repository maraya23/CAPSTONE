<?php

session_start();

if (!isset($_SESSION['admin_id'])) {

    header('Location: /CAPSTONE/login.php');
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Create User Account - e-Nurse</title>


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

    padding:25px;

    overflow:hidden;

    height:100vh;

    box-sizing:border-box;

}



.topbar{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:20px;

}


.topbar h1{

    margin:0;

    font-size:32px;

    color:#111827;

}



.welcome{

    background:white;

    padding:12px 20px;

    border-radius:15px;

    font-size:15px;

    box-shadow:0 3px 12px rgba(0,0,0,.08);

}



/* FORM */

.form-card{

    background:white;

    padding:25px;

    border-radius:20px;

    width:750px;

    box-shadow:0 4px 15px rgba(0,0,0,.08);

}



.form-card h2{

    margin-top:0;

    font-size:24px;

}


.form-group{

    margin-bottom:15px;

    position:relative;

}



label{

    font-weight:bold;

    font-size:14px;

}



input,
select{

    width:100%;

    box-sizing:border-box;

    padding:11px;

    margin-top:5px;

    border:1px solid #d1d5db;

    border-radius:10px;

    font-size:14px;

}



input:focus,
select:focus{

    outline:none;

    border-color:#2563eb;

}



button{

    background:#2563eb;

    color:white;

    border:none;

    padding:16px 35px;

    border-radius:12px;

    font-size:17px;

    cursor:pointer;

}


button:hover{

    background:#1d4ed8;

}


/* USERNAME SUGGESTION */

.username-box{

    position:relative;

}
    
#usernameSuggestions{

    width:100%;

    display:none;

    position:absolute;

    top:100%;

    left:0;

    margin-top:5px;

    background:white;

    border:1px solid #d1d5db;

    border-radius:10px;

    overflow:hidden;

    z-index:9999;

    box-shadow:0 4px 15px rgba(0,0,0,.15);

}


.suggestion{

    padding:12px;

    cursor:pointer;

    font-size:16px;

}



.suggestion:hover{

    background:#e8f0ff;

}



/* PASSWORD */

.password-box{

    position:relative;

}


.password-box input{

    padding-right:45px;

}


.password-box i{

    position:absolute;

    right:15px;

    top:50%;

    transform:translateY(-35%);

    cursor:pointer;

    color:#2563eb;

    font-size:17px;

}


.password-box i:hover{

    color:#1d4ed8;

}

.error{

    color:red;

    margin-bottom:15px;

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



<a href="manage_users.php" >

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


<h1>Create User Account</h1>



<div class="welcome">

<i class="fa-solid fa-user"></i>

Welcome, Admin

</div>


</div>





<div class="form-card">



<h2>

<i class="fa-solid fa-user-plus"></i>

Create New User

</h2>




<?php


if(isset($_GET['success'])){

echo "<p style='color:green; font-weight:bold;'>
User account created successfully.
</p>";

}


if(isset($_GET['error'])){


if($_GET['error']=="password"){

echo "<p class='error'>
Password must be at least 8 characters and contain a letter and number.
</p>";

}


if($_GET['error']=="username"){

echo "<p class='error'>
Username already exists.
</p>";

}


if($_GET['error']=="email"){

echo "<p class='error'>
Email already exists.
</p>";

}
}

    if($_GET['error']=="name"){

echo "<p class='error'>
Account with the same First Name and Surname already exists.
</p>";

}

?>






<form action="save_user.php" method="POST">



<div class="form-group">

<label>First Name</label>

<input 
type="text"
id="firstname"
name="firstname"
required>

</div>





<div class="form-group">

<label>Middle Name</label>

<input 
type="text"
name="middlename">

</div>





<div class="form-group">

<label>Surname</label>

<input 
type="text"
name="surname"
required>

</div>





<div class="form-group">

<label>Email</label>

<input 
type="email"
name="email">

</div>





<div class="form-group">

<label>Username</label>


<div class="username-box">

<input 
type="text"
id="username"
name="username"
autocomplete="off"
required>


<div id="usernameSuggestions"></div>

</div>

</div>





<div class="form-group">

<label>Password</label>


<div class="password-box">

<input 
type="password"
id="password"
name="password"
required>

<i class="fa-solid fa-eye" 
id="eyeIcon" 
onclick="showPassword()">
</i>

</div>

</div>
    
<div class="form-group">


<label>User Type</label>


<select name="usertype" required>


<option value="">Select User Type</option>


<option value="admin">
Admin
</option>


<option value="user">
User
</option>


</select>



</div>





<button type="submit" name="create">


<i class="fa-solid fa-user-plus"></i>

Create User


</button>




</form>



</div>




</main>



</div>






<script>


const firstname = document.getElementById("firstname");

const username = document.getElementById("username");

const suggestions = document.getElementById("usernameSuggestions");


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



fetch("suggest_user.php",{


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






function showPassword(){

    let password = document.getElementById("password");

    let eye = document.getElementById("eyeIcon");


    if(password.type === "password"){

        password.type = "text";

        eye.classList.remove("fa-eye");

        eye.classList.add("fa-eye-slash");

    }

    else{

        password.type = "password";

        eye.classList.remove("fa-eye-slash");

        eye.classList.add("fa-eye");

    }

}



</script>



</body>

</html>