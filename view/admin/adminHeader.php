<?php 
include '../../inc/config.php';

$user = new user();
if (!$user->isAdmin()){
  //http_response_code(401);
  die('You must be  an administrator to view this page.');
}

?>

<div class="rightbar">
  <h1>Navigation</h1>
  <ul class='options'>
  <?php 
    $adminpages = array(
      'home'=>'Admin Home',
      'galaxy'=>'Galaxy Editor',
      'government'=>'Governments'
    );
    foreach ($adminpages as $url => $page) {
      echo '<li><a href="admin/'.$url.'" class="load">'.$page.'</a></li>';
    }
  ?>
  </ul>
</div>

<script>
  footerInject('<a href="home" class="load">Return to game</span>');
</script>