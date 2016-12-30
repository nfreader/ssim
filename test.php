<?php
require_once ('inc/config.php');
?>

<!DOCTYPE html>
<html class="no-js" lang="en-us">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Space Sim V.<?php echo GAME_VERSION; ?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <body>
<?php

$syst = new syst(1);

var_dump($syst);