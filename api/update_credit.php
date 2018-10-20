<?php
// https://www.tutorialspoint.com/php/php_get_post.htm
$success = 0;

include("set_db.php");
$eid = $_POST["eid"];

///////////////////////
// convert name to uid
$uid = -1;
$sql = 'select user.uid, credit, agree, disagree from event,user where event.uid = user.uid and eid='.$eid;
if($debug)echo("query str".$sql."<br />\n");
$result = mysqli_query($conn, $sql);
if($result)
{
    while($row = mysqli_fetch_assoc($result))
    {
        $uid = $row['uid'];
        $credit = $row['credit'];
        $agree_sum = $row['agree'];
        $disagree_sum = $row['disagree'];
        $finalopinion = $agree_sum - $disagree_sum;
        if($finalopinion< 0)$finalopinion =0;
        print_r($row);
    }
    mysqli_free_result($result);
}
else $uid = -1;
/////////////////////

if($uid==-1) // || $super==0
{
    echo("Please create user<br />\n");
    $success = 0;
}
else{
    // update credit
    $newcredit = $credit + $finalopinion;
    $sqlcreate = 'update user set credit = '.$newcredit.' where uid ='.$uid;
    echo($sqlcreate);
    $createresult = mysqli_query($conn, $sqlcreate);
    echo('Update Results: '.((int)$createresult)."<br />\n");
    $success = (int)$createresult;
}
?>
<html>
   <body>
   
      <form action = "<?php $_PHP_SELF ?>" method = "POST">
        Event id: <input type = "text" name = "eid" /><br />
        <input type = "submit" />
      </form>
   </body>
</html>
