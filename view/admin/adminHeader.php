<script>
  setContent('.footerbar .pull-right','<a href="home" class="load">Return to game</span>');
  $('body').addClass('admin');
</script>
<?php 
include '../../inc/config.php';

$user = new user();
$game = new game();
if (!$user->isAdmin()){
  //http_response_code(401);
  $game->logEvent('UA','Attempted to access admin area');
  die('You must be  an administrator to view this page.');
}

if((!isset($_SESSION['sudo_mode'])) || (false === $_SESSION['sudo_mode'])) {
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
      'shipyard'=>'Shipyard',
      'government'=>'Governments',
      'commod'=>'Commodities',
      'mission'=>'Missions'
    );
    foreach ($adminpages as $url => $page) {
      echo '<li><a href="admin/'.$url.'" class="load">'.$page.'</a></li>';
    }
  ?>
  </ul>
</div>
