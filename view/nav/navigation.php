<?php
switch ($pilot->status) {
  case 'L':
    include('nav/landed.php');
  break;
  
  case 'S':
  default:
    include('nav/space.php');
    echo "<script>$('body').removeClass('bluespace');</script>";
  break;

  case 'B':
    include('nav/bluespace.php');
    echo "<script>$('body').addClass('bluespace');</script>";
  break;
}