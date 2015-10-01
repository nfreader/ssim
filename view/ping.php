<?php 
require_once('../inc/config.php');
$user = new user();
?>

<div class="footer">
  S.I.M.S. is &copy; <?php echo GAME_YEAR; ?> Checkhov Armaments. All rights reserved.
<?php if ($user->isLoggedIn()) :?>
  <p class="pull-right">
    <a class='local-action' action='logout' href='login'>Terminate Session</a>
  </p>
<?php else: ?>

<?php endif; ?>

</div>