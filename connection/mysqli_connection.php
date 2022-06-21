<?php
// PRODUCTION
// $host = "192.168.100.227";
// $username = "gibson_collage_user";
// $password = "gibson_collage_user";
// $db_name = "dandiboru";

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