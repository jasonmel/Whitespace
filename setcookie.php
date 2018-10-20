<?php
if (isset($_GET["uid"])) {
  setcookie("uid", $_GET["uid"],  time() + 60*60*24*365);
}
?>
