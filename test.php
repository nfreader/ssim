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
    <link rel="stylesheet" href="assets/css/style.css<?php echo SSIM_DEBUG?'?v='.rand():''?>">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet">
  </head>
  <body>
    <header>
      <div class="pull-center">&#x0226A; TESTING TESTING TESTING &#x0226B;</div>
    </header>
    <div id="game">
      <div id="center" class="full">
      <?php
      $pilot = new pilot('40abc4d3a95');
      var_dump($pilot);
      ?>
      </div>
    </div>
    <footer>
      <div class="pull-center">&#x0226A; TESTING TESTING TESTING &#x0226B;</div>
    </footer>