<?php 

require_once('../inc/config.php');
?>


<div class="footer">
  <?php $user = new user(); if($user->isLoggedIn()) {
      echo "<a href='#' class='action' action='logout'>Terminate Session</a>";
    } else {
      echo "Session not found";
    }?>
</div>