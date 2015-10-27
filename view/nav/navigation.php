<?php
switch ($pilot->status) {
  case 'L':
    include('nav/landed.php');
  break;

  case 'S':
  default:
    include('nav/space.php');
  break;

  case 'B':
    include('nav/bluespace.php');
  break;
}