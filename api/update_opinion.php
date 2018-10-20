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
$opinion = $_POST["opinion"]; // 1 click like, -1 click dislike

///////////////////////
// convert name to uid
$uid = -1;
$sql = 'select uid from user where name = "'.$uname.'"';
if($debug) echo "query str".$sql."<br />\n";
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
    // query like status
    $sql = 'select oid, opinion from opinion where uid = '.$uid.' and eid = '.$eid;
    if($debug)echo("query str".$sql."<br />\n");

    $result = mysqli_query($conn, $sql);
    if($result)
    {
        $agree_inc = 0;
        $disagree_inc = 0;
        $haveresult = 0;
        while($row = mysqli_fetch_assoc($result))
        {
            $haveresult = 1;
            if($row['opinion']!=$opinion)
            {
                // swap
                $oid = $row["oid"];
                $sqlcreate = 'update opinion set opinion = '.$opinion.' where oid = '.$oid;
                if($debug)echo($sqlcreate);
                $createresult = mysqli_query($conn, $sqlcreate);
                if($debug)echo('Update Results: '.((int)$createresult)."<br />\n");
                $success = (int)$createresult;
                if($opinion==1)
                {
                    $agree_inc = 1;
                    $disagree_inc = -1;
                }
                else{
                    $agree_inc = -1;
                    $disagree_inc = 1;
                }
            }
            else
            {
                // cancel
                $oid = $row["oid"];
                $sqlcreate = 'delete from opinion where oid = '.$oid;
                if($debug)echo($sqlcreate);
                $createresult = mysqli_query($conn, $sqlcreate);
                if($debug)echo('Delete Results: '.((int)$createresult)."<br />\n");
                $success = (int)$createresult;
                if($opinion==1)
                {
                    $agree_inc = -1;
                    $disagree_inc = 0;
                }
                else{
                    $agree_inc = 0;
                    $disagree_inc = -1;
                }
            }
            break;
        }
        mysqli_free_result($result);
        
        if($haveresult==0)
        {
            if($debug) echo "result does not exist".$sql."<br />\n";
            // create
            $formatstr = sprintf("%d, %d, %d", $uid, $eid, $opinion);
            $sqlcreate = 'INSERT INTO opinion(uid,eid,opinion) VALUES('.$formatstr.')';
            if($debug)echo($sqlcreate);
            $createresult = mysqli_query($conn, $sqlcreate);
            if($debug)echo('Create Results: '.((int)$createresult)."<br />\n");
            $success = (int)$createresult;
            if($opinion==1)
            {
                $agree_inc = 1;
                $disagree_inc = 0;
            }
            else{
                $agree_inc = 0;
                $disagree_inc = 1;
            }
        }
    }
    if($debug)echo('Final opinion:'.$opinion.'<br />');
    
    // update opinion
    $sql = 'select agree, disagree from event where eid = '.$eid;
    if($debug)echo("query str".$sql."<br />\n");
    $result = mysqli_query($conn, $sql);
    if($result)
    {
        while($row = mysqli_fetch_assoc($result))
        {
            $agree = (int)$row["agree"] + $agree_inc;
            $disagree = (int)$row["disagree"] + $disagree_inc;
            
            $sqlcreate = 'update event set agree = '.$agree.' where eid = '.$eid;
            if($debug)echo($sqlcreate);
            $createresult = mysqli_query($conn, $sqlcreate);
            if($debug)echo('Updates Results: '.((int)$createresult)."<br />\n");
            
            $sqlcreate = 'update event set disagree = '.$disagree.' where eid = '.$eid;
            if($debug)echo($sqlcreate);
            $createresult = mysqli_query($conn, $sqlcreate);
            if($debug)echo('Updates Results: '.((int)$createresult)."<br />\n");
        }
        mysqli_free_result($result);
    }
}
?>
