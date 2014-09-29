<?php

if (isset($_GET['msg'])) {
  echo "<script>notify('".$_GET['msg']."')</script>";
  //var_dump($_GET['msg']);
}

?>