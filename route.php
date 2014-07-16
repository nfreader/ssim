<?php 

if (isset($_GET['action'])) {
  if ($_GET['action'] === 'login') {
    if ($_POST['password'] != 123) {
      http_response_code(403);
    } else {
      http_response_code(200);
    }
  }
}