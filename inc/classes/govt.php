<?php 

class govt {

  public function listGovt($id=null) {
    $db = new database();
    if ($id === null) {
      $db->query("SELECT * FROM ssim_govt");
      $db->execute();
      return $db->resultSet();
    } else {
      $db->query("SELECT * FROM ssim_govt WHERE id = :id");
      $db->bind(':id',$id);
      $db->execute();
      return $db->single();
    }
  }

}