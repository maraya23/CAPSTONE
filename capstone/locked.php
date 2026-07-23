<?php

session_start();

if (!isset($_SESSION["lockout_until"])) {

    header("Location: login.php");
    exit();

}

$unlockTime = $_SESSION["lockout_until"];

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Account Locked</title>

</head>

<body>

<h1>

Account Temporarily Locked

</h1>

<p>

Too many incorrect verification codes were entered.

</p>

<p>

Please wait before trying again.

</p>

<h2 id="countdown">

Loading...

</h2>

<br>

<button
id="loginButton"
disabled>

Back to Login

</button>

<script>

const unlockTime = new Date("<?php echo $unlockTime; ?>").getTime();

const countdown = document.getElementById("countdown");

const button = document.getElementById("loginButton");

const timer = setInterval(function(){

    const now = new Date().getTime();

    const distance = unlockTime - now;

    if(distance <= 0){

        clearInterval(timer);

        countdown.innerHTML = "You may now log in.";

        button.disabled = false;

        button.onclick = function(){

            window.location.href = "login.php";

        };

        return;

    }

    const minutes = Math.floor(distance / (1000 * 60));

    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    countdown.innerHTML =
        minutes + ":" +
        String(seconds).padStart(2,"0");

},1000);

</script>

</body>

</html>