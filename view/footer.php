<?php
require_once('../inc/config.php');
$user = new user();
?>
<p class="pull-left">S.I.M.S.QTerm//Chekhov Armaments</p>
<p class="pull-right">

<?php if ($user->isLoggedIn()) :?>
    <a class="page" href='home'>
      <i class="fa fa-home"></i> </a> | <a class='action' href='logout' data-dest='login'>Terminate Session</a>
<?php endif; ?>
<?php if ($user->isAdmin()) :?>
    | <a class="page" href='admin/home'>Sudo Mode</a>
<?php endif; ?>
</p>
