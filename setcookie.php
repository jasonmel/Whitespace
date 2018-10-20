<?php
if (isset($_GET["id"])) {
  setcookie("id", $_GET["id"],  time() + 60*60*24*365);
}
?>
