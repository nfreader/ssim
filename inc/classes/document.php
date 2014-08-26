<?php 

class document {

  public $document;
  public $render;
  public $isRendered = false;

  public function __construct($id = NULL, $render = false) {
    if (($id != NULL) && ($render === true)) {
      $this->render = $this->renderDocument($id);
      $this->isRendered = true;
    } elseif($id != NULL) {
      $this->document = $this->retrieveDoc($id);
    }
  }

  public function newDocument($type, $owner, $data) {
    $db = new database();
    $db->query("INSERT INTO ssim_document
      (pilot, type, data, duid, timestamp)
      VALUES (:pilot, :type, :data, :duid, NOW())");
    $pilot = new pilot(true, true, $owner);
    $db->bind(':pilot',$pilot->pilot->id);
    $db->bind(':type',$type);
    //TODO: Define a list of valid document types
    $db->bind(':data',json_encode($data, JSON_NUMERIC_CHECK));
    $db->bind(':duid',hexPrint($pilot->pilot->id.$type.date(SSIM_DATE)));
    if($db->execute()) {
      return "Document generated";
    }
  }

  public function retrieveDoc($id) {
    $db = new database();
    $db->query("SELECT * FROM ssim_document
      WHERE ssim_document.id = :id");
    $db->bind(':id',$id);
    $db->execute();
    return $db->single();
  }

  public function renderDocument($id) {
    $doc = $this->retrieveDoc($id);
    $type = documentType($doc->type);
    $doc = json_encode($doc);
    $return = json_encode($type);
    $return = $return.$doc;
    return $return;
  }

}
