<?php
header("Content-Type: application/json; charset=UTF-8");

$comment1->id = 1;
$comment1->uid = "jasonmel";
$comment1->eid = 1;
$comment1->time = new DateTime('2018-10-19T20:03:01.012345Z');;
$comment1->comment = "Help! Help! NTNU Fire!!!";

$comment2->id = 2;
$comment2->uid = "whitety";
$comment2->eid = 1;
$comment2->time = new DateTime('2018-10-19T20:03:01.012345Z');;
$comment2->comment = "Who lets the dogs out?";

$comment3->id = 3;
$comment3->uid = "whitets";
$comment3->eid = 1;
$comment3->time = new DateTime('2018-10-19T20:03:01.012345Z');;
$comment3->comment = "Mom, I'm here... .__.\\~/";

$myArr = array($comment1, $comment2, $comment3);

$myJSON = json_encode($myArr);

echo $myJSON;
?>
