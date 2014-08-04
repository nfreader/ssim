<?php
require_once ('inc/config.php');

$user = new user();

require_once ('header.php');

if (isset($_GET['action'])) {

}

require_once ('footer.php');

if ($user->isLoggedIn()) {
	directLoad('view/home.php');
} elseif ((isset($_GET['action'])) && $_GET['action'] == 'logout') {
	//directLoad('view/login.php');
} else {
	directLoad('view/login.php');
}

?>