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
    <li><a href="admin/home" class="load">Admin Home</a></li>
    <li><a href="admin/galaxy" class="load">Galaxy Editor</a></li>
  </ul>
</div>

<script>
  footerInject('<span class="error">SUDO MODE</span>');
</script>