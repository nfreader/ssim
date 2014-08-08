<?php

include '../../inc/config.php';

$user  = new user();
if ($user->isLoggedIn() && $user->isAdmin()) {
  if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'addSpob') {
      $spob = new spob();
      $msg = $spob->addSpob($_GET['syst'],
        $_POST['name'],
        $_POST['type'],
        $_POST['techlevel'],
        $_POST['description']);
    }
    if ($action === 'addCommod') {
      $commod = new commod();
      $msg = $commod->addCommod($_POST['name'],
        $_POST['techlevel'],
        $_POST['baseprice'],
        $_POST['type']);
    }
  }
  echo $msg;
} else {
    echo "You must be logged in as an administrator! This incident has been reported!";
}