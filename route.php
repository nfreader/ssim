<?php

//Routing Handler Woo!
//If an integrity check fails here (IE: A missing parameter), return an HTTP
//status code in the 4xx or 5xx range. Annoy people with console errors!

//^^FUCK THAT NOISE. REWRITE TIME BITCHES

include 'inc/config.php';

if (isset($_GET['action'])) {
  $action = $_GET['action']; //Because I'm lazy and my ] key is broken.
  $user = new user();
  if ($action === 'register') {
    if ($_POST['password'] != $_POST['password-again']) {
      $msg = "Sorry, your passwords must match.";
    } else {
      $msg = $user->registerNewUser($_POST['username'],$_POST['password'],$_POST['email']);
    }
  } elseif ($action === 'login') {
    $msg = $user->logIn($_POST['username'],$_POST['password']);
  } elseif ($action === 'logout') {
    $_SESSION = '';
    session_destroy();
    $msg = "You have logged out";
  }
}

echo $msg;
