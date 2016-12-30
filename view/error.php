<div id="left" id="js">
<ul class="options">
  <li><a class="page" href="home">Back</a></li>
</ul>

</div>

<div id="center" class="wide">
<p class="ooc">
An error occurred. Use the back button to return to the previous screen. If the issue persists, please contact the administration.

<?php 
require_once("../inc/config.php");
var_dump($_SESSION);
var_dump($_GET);
var_dump($_POST);
var_dump($_SERVER);

die();
?>
</div>