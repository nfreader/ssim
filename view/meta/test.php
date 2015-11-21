<?php

require_once('../../inc/config.php');

$user = new user();

var_dump($user->activateUser($user->getUIDByUsername('test')));
?>
