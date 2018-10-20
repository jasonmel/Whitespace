<?php
$debug = 0;

$db_server = "localhost";

$db_name = "test";

$db_user = "nasa";

$db_passwd = "yourNASA";


$conn = @mysqli_connect($db_server, $db_user, $db_passwd);
if(!$conn)
    die("Failed in connection");

if(!@mysqli_select_db($conn, $db_name))
    die("Failed in db selection");
?>
