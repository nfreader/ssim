<?php
require_once('inc/config.php');

$user = new user();

echo "<div id='game'>";

echo "</div>";


require_once('footer.php');

if ($user->isLoggedIn()) {
  require_once('header.php');
  //include 'view/home.php';
  directLoad('view/home.php');
} else {
  require_once('header.php');
  echo "<div id='game'>";
  //include 'view/login.php';
  directLoad('view/login.php');
  echo "</div>";
}

?>