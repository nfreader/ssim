<?php

class message {

  public $inbox;
  public $conversation;

  public function __construct($convoid=null,$empty=FALSE) {
    if (TRUE === $empty) {
      //We're instantiating this class so we can send a mesage
      //And we don't need to pull anything down
      //$message = new message(NULL,TRUE)
    } else {
      if (NULL === $convoid) {
        $this->inbox = $this->getPilotInbox();
      } else {
        $this->conversation = new stdclass;
        $this->conversation->posts = $this->getThread($convoid);
        $this->conversation->with = new stdclass;
        $this->conversation->with->id = $convoid;
        $this->conversation->with->name = $this->getNameByID($convoid);
      }
    }
  }

  //Messages are simple one-to-one threads without subjects or anything else.
  //Just because anything more than that is silly and a huge pain in the butt
  //If a message sender is 0, that means this is a system message and that it
  //needs to have a specific sender set.

  //Node IDs are hexprint(systname.systid). Tricky part is getting those
  //values without grabbing a bigass $pilot->pilot object. Ugh.

  public function newPilotMessage($to,$content) {
    if(empty(trim($content))) {
      return returnError('Message cannot be empty!');
    }
    if(empty(trim($to))) {
      return returnError('Invalid recipient.');
    }
    $sender = new pilot(NULL,TRUE);
    $receiver = new pilot($to,TRUE);
    if ($sender->uid == $receiver->uid) {
      return returnError("You can't send yourself a message!");
    }
    $db = new database();
    $db->query("INSERT INTO ssim_message
      (msgto, msgfrom, messagebody, sendnode, recvnode, timestamp)
      VALUES (:msgto, :msgfrom, :messagebody, :sendnode, :recvnode, NOW())");
    $db->bind(':msgto', $to);
    $db->bind(':msgfrom', $sender->uid);
    $db->bind(':messagebody', $content);
    $db->bind(':sendnode', $this->getNodeID($sender->uid));
    $db->bind(':recvnode', $this->getNodeID($receiver->uid));
    if ($db->execute()){
      return returnSuccess("Message sent to ".$receiver->name);
    }
  }

  /* newSystemMessage
   *
   * Sends a message to the pilot that shows up as coming from a non-pilot
   * sender. IE: "Message from Commodity Center" etc etc.
   *
   * TODO: 
   * - Roll in notification support
   * - Related to above, roll in priority messages
   *
   * @to (int) (optional) Who the message is going to
   * @from (string) Who the message will appear as being from
   * @content (string) Message body.
   *
   * @return (string) A string indicating success or failure.
   *
  */

  public function newSystemMessage($to,$from,$content) {
    if(isEmpty($content)) {
      return 'Message cannot be empty!';
    }
    $receiver = new pilot($to,TRUE);
    $db = new database();
    $db->query("INSERT INTO ssim_message
      (msgto, msgfrom, messagebody, fromoverride, recvnode, timestamp)
      VALUES (:msgto, :msgfrom, :messagebody, :fromoverride, :recvnode, NOW())");
    $db->bind(':msgto', $to);
    $db->bind(':msgfrom', 0);
    $db->bind(':messagebody', $content);
    $db->bind(':recvnode', $this->getNodeID($receiver->uid));
    $db->bind(':fromoverride',$from);
    if ($db->execute()){
      return "Message sent to ".$receiver->name."!";
    }
  }

  public function getNodeID($pilot) {
    $db = new database();
    $db->query("SELECT ssim_syst.id,
      ssim_syst.coord_x,
      ssim_syst.coord_y,
      ssim_syst.name
      FROM ssim_pilot
      LEFT JOIN ssim_syst ON ssim_pilot.syst = ssim_syst.id
      WHERE ssim_pilot.uid = :pilot");
    $db->bind(':pilot',$pilot);
    $db->execute();
    //return $db->single();
    //Wait wait wait, why would we do that when we can easily hexprint() it?
    $node = $db->single();
    return hexprint($node->name.$node->coord_x.$node->coord_y);
  }

  public function getPilotInbox() {
    $db = new database();
    $db->query("SELECT IF(ssim_message.msgfrom = 0, 'SYSTEM MESSAGE', ssim_pilot.name) AS sender,
      SUM(IF(ssim_message.read = 0, 1, 0)) AS unread,
      MAX(ssim_message.timestamp) AS timestamp,
      ssim_message.msgfrom
      FROM ssim_message
      LEFT JOIN ssim_pilot ON ssim_message.msgfrom = ssim_pilot.uid
      WHERE ssim_message.msgto = ?
      GROUP BY ssim_message.msgfrom
      ORDER BY ssim_message.timestamp DESC");
    $pilot = new pilot(NULL,TRUE);
    $db->bind(1,$pilot->uid);
    $db->execute();
    return $db->resultSet();
  }

  public function getOutbox() {
    $db = new database();
    $db->query("SELECT ssim_message.msgto,
    ssim_pilot.name,
    MAX(ssim_message.timestamp) as timestamp,
    count(ssim_message.messagebody) AS msgcount,
    IF (ssim_message.msgfrom = 0, 0, 1) AS system,
    ssim_message.msgfrom AS msgfromid
    FROM ssim_message
    JOIN ssim_pilot ON ssim_message.msgto = ssim_pilot.uid
    WHERE ssim_message.msgfrom = ?
    GROUP BY ssim_message.msgto
    ORDER BY ssim_message.timestamp DESC");
    $pilot = new pilot(NULL, true);
    $db->bind(1,$pilot->uid);
    $db->execute();
    return $db->resultSet();
  }

  public function getThread($convo) {
    'sys' == $convo ? $convo = 0 : $convo = $convo;
    $db = new database();
    $db->query("SELECT IF(ssim_message.msgfrom = 0, ssim_message.fromoverride, ssim_pilot.name) AS sender,
      ssim_message.timestamp,
      ssim_message.msgfrom,
      ssim_message.messagebody,
      ssim_message.recvnode,
      ssim_message.sendnode,
      IF (ssim_message.msgfrom = 0, '[AUTOMATED SYSTEM MESSAGE]', ssim_pilot.fingerprint) AS fingerprint,
      ssim_message.read,
      ssim_message.id
      FROM ssim_message
      LEFT JOIN ssim_pilot ON ssim_message.msgfrom = ssim_pilot.uid
      WHERE (ssim_message.msgto = ? AND ssim_message.msgfrom = ?)
      OR (ssim_message.msgto = ? AND ssim_message.msgfrom = ?)
      ORDER BY ssim_message.timestamp DESC;");
    $pilot = new pilot(NULL, true);
    $db->bind(1,$pilot->uid);
    $db->bind(2,$convo);
    $db->bind(3,$convo);
    $db->bind(4,$pilot->uid);
    $db->execute();
    return $db->resultSet();
  }

  public function markMessageRead($id) {
    $db = new database();
    $db->query("UPDATE ssim_message SET ssim_message.read = 1 
    WHERE ssim_message.id = :id");
    $db->bind(':id',$id);
    $db->execute();
  }

  public function deleteMessage($id) {
    $db = new database();
    $db->query("DELETE FROM ssim_message
      WHERE id = :id
      AND ssim_message.msgto = :pilot");
    $pilot = new pilot(NULL, true);
    $db->bind(':id',$id);
    $db->bind(':pilot',$pilot->uid);
    if ($db->execute()) {
      return returnSuccess("Message deleted");
    } else {
      return returnError("Unable to delete message");
    }
  }

    public function deleteMessageThread($fromid) {
    'sys' == $fromid ? $fromid = 0 : $fromid = $fromid;
    $db = new database();
    $db->query("DELETE FROM ssim_message
      WHERE ssim_message.msgto = :pilot
      AND ssim_message.msgfrom = :fromid");
    $pilot = new pilot(NULL, true);
    $db->bind(':fromid',$fromid);
    $db->bind(':pilot',$pilot->uid);
    if ($db->execute()) {
      return returnSuccess("Thread deleted");
    } else {
      return returnError("Unable to delete thread");
    }
  }

  public function getUnreadCount() {
    $db = new database();
    $db->query("SELECT count(*) AS count FROM ssim_message
      WHERE ssim_message.msgto = :pilot
      AND ssim_message.read = 0");
    $pilot = new pilot(true, true);
    $db->bind(':pilot',$pilot->uid);
    $db->execute();
    return $db->single()->count;
  }

  public function getNameByID($id) {
    if (0 === $id || 'sys' === $id) {
      return "SYSTEM MESSAGE";
    } else {
      $pilot = new pilot($id,TRUE);
      return $pilot->name;
    }
  }

}
