<?php
// DEVELOPMENT
$host = "localhost";
$username = "root";
$password = "";
$db_name = "dandiboru";

$con = mysqli_connect($host, $username, $password, $db_name);

if (!$con) {

  die(mysqli_error($con));

}

?>