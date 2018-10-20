<?php
// https://www.tutorialspoint.com/php/php_get_post.htm
$success = 0;

include("set_db.php");
if(!isset($_POST["uname"]))
{
    echo('undefine');
}
$lat = $_POST["lat"];
$lon = $_POST["lon"];
$description = $_POST["description"];
$title = $_POST["title"];

///////////////////////
// convert name to uid
$uid = -1;
$sql = 'select uid from user where name = "'.$_POST["uname"].'"';
if($debug)echo("query str".$sql."<br />\n");
$result = mysqli_query($conn, $sql);
if($result)
{
    while($row = mysqli_fetch_assoc($result))
    {
        $uid = $row['uid'];
    }
    mysqli_free_result($result);
}
else $uid = -1;
/////////////////////

if($uid==-1)
{
    echo("Please create user<br />\n");
    $success = 0;
}
else{
    // create event
    $formatstr = sprintf("%d, %f, %f, %d, 0, '%s', '%s'", $uid, $lat, $lon, time(),mysqli_real_escape_string($conn,$title),mysqli_real_escape_string($conn,$description));
    $sqlcreate = 'INSERT INTO event(uid,lat,lon,date,status,title,description) VALUES('.$formatstr.')';
    if($debug)echo($sqlcreate);
    $createresult = mysqli_query($conn, $sqlcreate);
    if($debug)echo('Create Results: '.((int)$createresult)."<br />\n");
    $success = (int)$createresult;
    echo $success;
}
?>
