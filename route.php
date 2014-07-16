<?php 

//Routing Handler Woo! 
//If an integrity check fails here (IE: A missing parameter), return an HTTP
//status code in the 4xx or 5xx range. Annoy people with console errors! 

require_once('inc/config.php');

if (isset($_GET['action'])) {

  if($_GET['action'] === 'register') {
    if ($_POST['password'] != $_POST['password-again']) {
      http_response_code(405);
    } else {
      $user = new user();
      if ($user->registerNewUser(
        $_POST['username'],
        $_POST['password'],
        $_POST['email']
      )) {
        http_response_code(200);
      } else {
        http_response_code(405);
      }
    }
  } elseif ($_GET['action'] === 'login') {
    $user = new user();
    if (!$user->logIn(
      $_POST['username'],
      $_POST['password']
    )) {
      http_response_code(403);
    } else {
      http_response_code(200);
    }
  }
  elseif ($_GET['action'] === 'logout') {
    $_SESSION = '';
    session_destroy();
    $msg = urlencode("This session has been terminated");
    directLoad('view/login.php?msg='.$msg);
  }

}