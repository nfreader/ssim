<?php
    require_once('inc/config.php');

    $user = new user();
    if ($user->isLoggedIn()) {

    } else {

    }
    require_once('header.php');
?>

<?php require_once('footer.php'); ?>