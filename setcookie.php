<?php
if (isset($_GET["uid"])) {
  $uid = $_GET["uid"];
  setcookie("uid", $uid,  time() + 60*60*24*365);

  include("./api/set_db.php");
  $super = 0;

  $formatstr = sprintf("0, %d, '%s'", $super, $uid);
  $sqlcreate = 'INSERT INTO user (credit,super,name) VALUES('.$formatstr.')';
  echo $sqlcreate;
  $createresult = mysqli_query($conn, $sqlcreate);
  echo (int)$createresult;
}
?>
