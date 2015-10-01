<?php
require_once ('header.php');

require_once ('footer.php');

if (TRUE) {
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