<?php
header("Content-Type: application/json; charset=UTF-8");

$fire1->id = 1;
$fire1->title = "NTNU Fire";
$fire1->reporter = "jasonmel";
$fire1->time = new DateTime('2018-10-19T20:03:01.012345Z');;
$fire1->lat = 25.026072;
$fire1->lon = 121.527535;
$fire1->description = "Help! Help! NTNU Fire!!!";
$fire1->status = 0;
$fire1->like = 12;
$fire1->dislike = 1;
$fire1->liked = false;
$fire1->disliked = true;

$fire2->id = 2;
$fire2->title = "NTU Fire";
$fire2->reporter = "whitety";
$fire2->time = new DateTime('2018-10-20T08:01:02.012345Z');;
$fire2->lat = 25.016844;
$fire2->lon = 121.539710;
$fire2->description = "Who lets the dogs out?";
$fire2->status = 1;
$fire2->like = 24;
$fire2->dislike = 3;
$fire2->liked = false;
$fire2->disliked = false;

$fire3->id = 3;
$fire3->title = "Daan Forest Park Fire";
$fire3->reporter = "whitets";
$fire3->time = new DateTime('2018-10-21T09:13:19.012345Z');;
$fire3->lat = 25.031807;
$fire3->lon = 121.535939;
$fire3->description = "Mom, I'm here... .__.\~/";
$fire3->status = 2;
$fire3->like = 101;
$fire3->dislike = 5;
$fire2->liked = true;
$fire2->disliked = false;

$myArr = array($fire1, $fire2, $fire3);

$myJSON = json_encode($myArr);

echo $myJSON;
?>
