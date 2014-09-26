<?php
require_once ('inc/config.php');

$user = new user();

require_once ('header.php');

require_once ('footer.php');

if ($user->isLoggedIn()) {
  if(isset($_GET['direct'])) {
    directLoad('view/'.$_GET['direct'].'.php');
  } else {
    directLoad('view/home.php');
  }
} elseif ((isset($_GET['action'])) && $_GET['action'] == 'logout') {
	//directLoad('view/login.php');
} else {
	directLoad('view/login.php');
}

?>