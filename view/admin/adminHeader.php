<?php 
include '../../inc/config.php';

$user = new user();
$game = new game();
if (!$user->isAdmin()){
  //http_response_code(401);
  $game->logEvent('UA','Attempted to access admin area');
  die('You must be  an administrator to view this page.');
}

if($_SESSION['sudo_mode'] === false) {
  $game->logEvent('SU','Sudo mode engaged');
  $_SESSION['sudo_mode'] = true;
}

?>

<div class="leftbar">
  <h1>Navigation</h1>
  <ul class='options'>
  <?php 
    $adminpages = array(
      'home'=>'Admin Home',
      'log'=>'Activity Log',
      'galaxy'=>'Galaxy Editor',
      'government'=>'Governments',
      'commodities'=>'Commodities',
      'commod-stats'=>'Commodity Stats',
      'mission'=>'Missions',
      'sysmsg'=>'System Message'
    );
    foreach ($adminpages as $url => $page) {
      echo '<li><a href="admin/'.$url.'" class="load">'.$page.'</a></li>';
    }
  ?>
  </ul>
</div>

<script>
  footerInject('<a href="home" class="load">Return to game</span>');
  $('body').addClass('admin');
</script>