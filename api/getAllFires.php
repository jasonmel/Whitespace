<?php
header("Content-Type: application/json; charset=UTF-8");

$fire1->id = 1;
$fire1->title = "NTNU Fire";
$fire1->reporter = "jasonmel";
$fire1->date = new DateTime('2018-10-19T20:03:01.012345Z');;
$fire1->lat = 25.026072;
$fire1->lon = 121.527535;
$fire1->description = "Help! Help! NTNU Fire!!!";

$fire2->id = 2;
$fire2->title = "NTU Fire";
$fire2->reporter = "whitety";
$fire2->date = new DateTime('2018-10-20T08:01:02.012345Z');;
$fire2->lat = 25.016844;
$fire2->lon = 121.539710;
$fire2->description = "Who lets the dogs out?";

$fire3->id = 3;
$fire3->title = "Daan Forest Park Fire";
$fire3->reporter = "whitets";
$fire3->date = new DateTime('2018-10-21T09:13:19.012345Z');;
$fire3->lat = 25.031807;
$fire3->lon = 121.535939;
$fire3->description = "Mom, I'm here... .__.\~/";

$myArr = array($fire1, $fire2, $fire3);

$myJSON = json_encode($myArr);

echo $myJSON;
?>