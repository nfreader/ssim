<?php
require_once('../../inc/config.php');
$pilot = new pilot(NULL,TRUE);
?>

<div id="left">
  <ul class="options">
    <li><a class="page" href="home">Back</a></li>
  </ul>
</div>

<?php
if(isset($_GET['govtid'])) :
  $govt = new govt($_GET['govtid'],TRUE);
  include('viewGov.php');
else :

endif;
