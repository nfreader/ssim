<?php 
require_once('../inc/config.php');
?>

<div class="footer">
  <?php $user = new user(); if($user->isLoggedIn()) {
      echo "<a href='#' class='action' action='logout'>Terminate Session</a>";
      if($user->isAdmin()) {
        echo "<div class='pull-right'><a class='load' href='admin/home'>Admin Panel</a></div>";
      }
    } else {
      echo "Session not found";
    }?>
</div>