<?php 

class document {
  public function __construct() {

  }

  public function newDocument($type, $owner, $data) {
    $db = new database();
    $db->query("INSERT INTO ssim_document
      (pilot, type, data, duid, timestamp)
      VALUES (:pilot, :type, :data, :duid, NOW())");
    $pilot = new pilot(true, true, $owner);
    $db->bind(':pilot',$pilot->pilot->id);
    $db->bind(':type',$type);
    $db->bind(':data',json_encode($data));
    $db->bind(':duid',hexPrint($pilot->pilot->id.$type.date(SSIM_DATE)));
    if($db->execute()) {
      return "Document generated";
    }
  }
}