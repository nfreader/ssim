<?php
switch ($pilot->status) {
  case 'L':
    include('nav/landed.php');
  break;
  
  case 'S':
  default:
    echo "<script>$('#game').removeClass('bluespace');</script>";
    include('nav/space.php');
  break;

  case 'B':
    echo "<script>$('#game').addClass('bluespace');</script>";
    include('nav/bluespace.php');
  break;
}