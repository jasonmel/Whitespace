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
$status = $_POST["status"];

///////////////////////
// convert name to uid
$uid = -1;
$sql = 'select uid,super from user where name = "'.$uname.'"';
if($debug)echo("query str".$sql."<br />\n");
$result = mysqli_query($conn, $sql);
if($result)
{
    while($row = mysqli_fetch_assoc($result))
    {
        $uid = $row['uid'];
        $super = $row['super'];
    }
    mysqli_free_result($result);
}
else $uid = -1;
/////////////////////

if(($uid==-1) || ($super==0))
{
    echo("Please create user<br />\n");
    $success = 0;
}
else{
    // solve credit
    $sqlcreate = 'update event set status = '.$status.' where eid ='.$eid;
    echo($sqlcreate);
    $createresult = mysqli_query($conn, $sqlcreate);
    if($debug)echo('Update Results: '.((int)$createresult)."<br />\n");
    $success = (int)$createresult;
}
?>
