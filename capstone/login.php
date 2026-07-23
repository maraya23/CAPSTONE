<?php

session_start();

require_once "includes/flash.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Login</title>

</head>

<body>

<div class="container">

<div class="form-box">

<form action="login_process.php" method="POST">

<h1>Log in</h1>

<?php

showFlash();

?>

<p>

<input
type="text"
name="username"
placeholder="Username"
required>

</p>

<p>

<input
type="password"
id="password"
name="password"
placeholder="Password"
required>

</p>

<p>

<label>

<input
type="checkbox"
id="showPassword">

Show Password

</label>

</p>

<p>

<button type="submit">

Log in

</button>

</p>

</form>

</div>

</div>

<script>

const password = document.getElementById("password");

const showPassword = document.getElementById("showPassword");

showPassword.addEventListener("change", function(){

    password.type = this.checked ? "text" : "password";

});

</script>

</body>

</html>