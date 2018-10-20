<?php
header("Content-Type: application/json; charset=UTF-8");

include("set_db.php");
// $minlat = $_GET["minlat"];
// $maxlat = $_GET["maxlat"];
// $minlon = $_GET["minlon"];
// $maxlon = $_GET["maxlon"];

// $sql = 'select eid as id, title, name as reporter, date, lat, lon, description, finalopinion from event, user where lat >= '.$minlat.' and lat <= '.$maxlat.' and lon >= '.$minlon.' and lon <= '.$maxlon.' and event.uid = user.uid ';
if(!isset($_GET["uname"]))
{
    echo('undefine');
}
$uname = $_GET["uname"];

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
        $uid_user = $row['uid'];
    }
    mysqli_free_result($result);
}
else $uid_user = -1;
/////////////////////

if($uid_user==-1)
{
    echo("Please create user<br />\n");
    $success = 0;
}

$sql = 'select eid as id, title, name as reporter, date, lat, lon, description, agree as agreesum, disagree as disagreesum, status from event, user where event.uid = user.uid ';
mysqli_query($conn, "SET NAMES utf8");

if($debug)echo("query str:".$sql."<br />\n");
$result = mysqli_query($conn, $sql);

if($result)
{
    $totalcnt = 0;
    $stack = Array();
    while($row = mysqli_fetch_assoc($result))
    {
        $timestamp = $row["date"] ;
        $row["date"] = gmdate("Y-m-d\TH:i:s\Z", $timestamp);
        $row["lat"] = (float)$row["lat"];
        $row["lon"] = (float)$row["lon"];
        $row["agree"] = 0;
        $row["disagree"] = 0;
        $row["agreesum"] = (int)$row["agreesum"];
        $row["disagreesum"] = (int)$row["disagreesum"];
        $row["status"] = (int)$row["status"];
        $totalcnt = $totalcnt + 1;
        array_push($stack, $row);
    }
    mysqli_free_result($result);
    
    for($i=0; $i < $totalcnt; $i++)
    {
        $sql2 = 'select opinion from opinion where eid = '.$stack[$i]["id"].' and uid ='.$uid_user;
        if($debug) echo "query str:".$sql2."<br />\n";
        $result2 = mysqli_query($conn, $sql2);
        if($result2)
        {
            while($row = mysqli_fetch_assoc($result2))
            {
                if($row["opinion"]==1)
                {
                    $stack[$i]["agree"]=true;
                    $stack[$i]["disagree"]=false;
                }
                elseif($row["opinion"]==-1)
                {
                    $stack[$i]["agree"]=false;
                    $stack[$i]["disagree"]=true;
                }
                break;
            }
            mysqli_free_result($result2);
        }
    }
    
    $json = json_encode($stack);

    echo $json;
}

mysqli_close($conn);
?>
