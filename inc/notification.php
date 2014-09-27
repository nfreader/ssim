<?php

if (isset($_GET['msg'])) {
  echo '<div class="notification">';
  //echo urldecode($_GET['msg']);
  echo "<script>showText('.notification','".urldecode($_GET['msg'])."',0,5);</script>";
  echo '</div>';
}

?>