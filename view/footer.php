<?php
require_once('../inc/config.php');
$user = new user();
?>
<div class="pull-left">S.I.M.S.QTerm//Checkhov Armaments</div>
<?php if ($user->isLoggedIn()) :?>
  <div class="pull-right">
    <a class="page" href='home'>
      <i class="fa fa-home"></i> </a> | <a class='action' href='logout' data-dest='login'>Terminate Session</a>
<?php endif; ?>
<?php if ($user->isAdmin()) :?>
    | <a class="page" href='admin/home'>Sudo Mode</a>
<?php endif; ?>
</div>
