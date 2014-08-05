<?php 
require_once('../inc/config.php');
?>

<div class="footer">
  <?php $user = new user(); if($user->isLoggedIn()) {
      echo "<a class='local-action' action='logout' href='login'>Terminate Session</a> ".date('c');
      if($user->isAdmin()) {
        echo "<div class='pull-right'>";
        echo "<a class='load' href='admin/home'>Admin Panel</a>";
        echo "</div>";
      }
    } else {
      echo 'Ship Integrated Management System V. '.GAME_VERSION;
      echo 'is © '.$year.' by Chekhov Armaments LTD. All rights reserved.';

    }?>
</div>