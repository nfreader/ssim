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
    <link rel="stylesheet/less" type="text/css" href="assets/css/style.less" />
    <script>
      less = {
        env: "development",
        async: false,
        fileAsync: false,
        poll: 1000,
        functions: {},
      };
    </script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/less.js/1.6.3/less.min.js"></script>
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet">
  </head>
  <body>
    <div class="loading">
      <h1 style="text-align: center; color: rgb(0,255,0); font-size: 500%;  margin: auto 25%; font-family: Monospace;">LOADING</h1>
    </div>
    <div class="headerbar">
      <div class="pull-left">
        <?php echo 'S.I.M.S. V. '.GAME_VERSION; ?>
      </div>
      <div class="pull-right">
        <?php echo date(SSIM_DATE);?>
      </div>
      <ul class="msglist"></ul>
    </div>
    <div class="helpText"></div>
    <div id="game">