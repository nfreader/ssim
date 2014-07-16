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
        http_response_code(405);
      } else {
        http_response_code(200);
      }
    }
  }

  if ($_GET['action'] === 'login') {
    if ($_POST['password'] != 123) {
      http_response_code(403);
    } else {
      http_response_code(200);
    }
  }
}