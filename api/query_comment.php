<?php
header("Content-Type: application/json; charset=UTF-8");
$eid = $_GET["eid"];

$sql = 'select user.name as name, comment, time as date from user, comment where user.uid = comment.uid and eid ='.$eid.' order by time DESC';

include("set_db.php");
mysqli_query($conn, "SET NAMES utf8");

if($debug) echo "query str:".$sql."<br />\n";
$result = mysqli_query($conn, $sql);

if($result)
{
    $stack = Array();
    while($row = mysqli_fetch_assoc($result))
    {
        $timestamp = $row["date"] ;
        $row["date"] = gmdate("Y-m-d\TH:i:s\Z", $timestamp);
        array_push($stack, $row);
    }
    $json = json_encode($stack);
    echo($json);
    mysqli_free_result($result);
}

mysqli_close($conn);
?>
