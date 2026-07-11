<?php

$host = "sql104.infinityfree.com";
$user = "if0_42371645";
$pass = "d3b5iCBMhnkPq7j";
$db = "if0_42371645_capstonedb";


$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

?>