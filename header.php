<?php
require_once ('inc/config.php');
$user = new user();
?>

<!DOCTYPE html>
<html class="no-js" lang="en-us">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Space Sim V.<?php echo GAME_VERSION; ?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css<?php echo SSIM_DEBUG?'?v='.rand():''?>">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet">
  </head>
  <body>
    <div class="loading">
      <h1 style="text-align: center; color: rgb(0,255,0); font-size: 500%;  margin: auto 25%; font-family: Monospace; z-index: 10000; position: absolute; left: 0; right: 0; display: block;">LOADING</h1>
    </div>
    <i class="fa fa-cog green" id="spinner"></i>
    <header>
      <p class="left">
        <?php echo 'S.I.M.S. V. '.GAME_VERSION; ?>
      </p>
      <p class="right" id="clock"><?php $year = GAME_YEAR; echo date("G:i:s d.m.$year");?></p>
    </header>
    <ul id="notifications">
    </ul>
    <div class="helpText"></div>
    <section id="game">
