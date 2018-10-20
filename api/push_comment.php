<?php
// https://www.tutorialspoint.com/php/php_get_post.htm
$success = 0;

include("set_db.php");
if(!isset($_POST["uname"]))
{
    echo('undefine');
}
$uname = $_POST["uname"];
$eid = $_POST["eid"];
$comment = $_POST["comment"];

///////////////////////
// convert name to uid
$uid = -1;
$sql = 'select uid from user where name = "'.$uname.'"';
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
    // push comment
    $formatstr = sprintf("%d, %d, %d, '%s'", $uid, $eid, time(), mysqli_real_escape_string($conn,$comment));
    $sqlcreate = 'INSERT INTO comment(uid,eid,time,comment) VALUES('.$formatstr.')';
    if($debug)echo($sqlcreate);
    $createresult = mysqli_query($conn, $sqlcreate);
    if($debug)echo('Create Results: '.((int)$createresult)."<br />\n");
    $success = (int)$createresult;
}
?>
