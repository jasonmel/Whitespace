<?php
// https://www.tutorialspoint.com/php/php_get_post.htm
$success = 0;

include("set_db.php");
if(!isset($_POST["name"]))
{
    echo('undefine');
}
$eid = $_POST["eid"];
$status = $_POST["status"];

///////////////////////
// convert name to uid
$uid = -1;
$sql = 'select uid,super from user where name = "'.$_POST["name"].'"';
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
<html>
   <body>
   
      <form action = "<?php $_PHP_SELF ?>" method = "POST">
        User name: <input type = "text" name = "name" /><br />
        Event id: <input type = "text" name = "eid" /><br />
        Status (0/1/2): <input type = "text" name = "status" /><br />
        <input type = "submit" />
      </form>
   </body>
</html>
