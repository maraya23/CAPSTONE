<?php

$host = "sql102.infinityfree.com";
$user = "if0_42342622";
$pass = "clavacio4114";
$db = "if0_42342622_capstonedb";


$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

?>