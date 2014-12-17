<?php

class message {

  //Messages are simple one-to-one threads without subjects or anything else.
  //Just because anything more than that is silly and a huge pain in the butt
  //If a message sender is 0, that means this is a system message and that it
  //needs to have a specific sender set.

  //Node IDs are hexprint(systname.systid). Tricky part is getting those
  //values without grabbing a bigass $pilot->pilot object. Ugh.

  public function newPilotMessage($to,$content) {
    if(isEmpty($content)) {
      return 'Message cannot be empty!';
    }
    $sender = new pilot(true, true);
    $receiver = new pilot(true, true, $to);
    if ($sender->pilot->id == $receiver->pilot->id) {
      return "You can't send yourself a message! ".$sender->pilot->name. " to ".$receiver->pilot->name." via ".$to;
    }
    $db = new database();
    $db->query("INSERT INTO ssim_message
      (msgto, msgfrom, messagebody, sendnode, recvnode, timestamp)
      VALUES (:msgto, :msgfrom, :messagebody, :sendnode, :recvnode, NOW())");
    $db->bind(':msgto', $to);
    $db->bind(':msgfrom', $sender->pilot->id);
    $db->bind(':messagebody', $content);
    $db->bind(':sendnode', $this->getNodeID($sender->pilot->id));
    $db->bind(':recvnode', $this->getNodeID($receiver->pilot->id));
    if ($db->execute()){
      return "Message sent to ".$receiver->pilot->name."!";
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
    $receiver = new pilot(true, true, $to);
    $db = new database();
    $db->query("INSERT INTO ssim_message
      (msgto, msgfrom, messagebody, fromoverride, recvnode, timestamp)
      VALUES (:msgto, :msgfrom, :messagebody, :fromoverride, :recvnode, NOW())");
    $db->bind(':msgto', $to);
    $db->bind(':msgfrom', 0);
    $db->bind(':messagebody', $content);
    $db->bind(':recvnode', $this->getNodeID($receiver->pilot->id));
    $db->bind(':fromoverride',$from);
    $game = new game();
    $game->heartbeat($to, 'newmsg', null, "You have a new message from $from");
    if ($db->execute()){
      return "Message sent to ".$receiver->pilot->name."!";
    }
  }

  public function getNodeID($pilot) {
    $db = new database();
    $db->query("SELECT ssim_syst.id,
      ssim_syst.name
      FROM ssim_pilot
      LEFT JOIN ssim_syst ON ssim_pilot.syst = ssim_syst.id
      WHERE ssim_pilot.id = :pilot");
    $db->bind(':pilot',$pilot);
    $db->execute();
    //return $db->single();
    //Wait wait wait, why would we do that when we can easily hexprint() it?
    $node = $db->single();
    return(hexprint($node->id.$node->name));
  }

  public function getPilotThreads() {
    $db = new database();
    $db->query("SELECT ssim_message.msgfrom,
      IF (ssim_message.msgfrom = 0, ssim_message.fromoverride,
        ssim_pilot.name) AS msgfrom,
      ssim_message.timestamp,
      ssim_message.read,
      count(ssim_message.messagebody) AS msgcount,
      IF (ssim_message.msgfrom = 0, 0, 1) AS system,
      ssim_message.msgfrom AS msgfromid,
      (SELECT
      count(ssim_message.id)
      FROM ssim_message 
      WHERE ssim_message.read = false
      AND ssim_message.msgto = 4
      ) AS unread
      FROM ssim_message
      LEFT JOIN ssim_pilot ON ssim_message.msgfrom = ssim_pilot.id
      WHERE msgto = :pilot
      GROUP BY ssim_message.msgfrom, ssim_message.fromoverride
      ORDER BY timestamp DESC");
    $pilot = new pilot(true, true);
    $db->bind(':pilot',$pilot->pilot->id);
    $db->execute();
    return $db->resultSet();
  }

  public function getPilotThreadsSent() {
    $db = new database();
    $db->query("SELECT ssim_message.msgto,
    ssim_pilot.name,
    ssim_message.timestamp,
    count(ssim_message.messagebody) AS msgcount,
    IF (ssim_message.msgfrom = 0, 0, 1) AS system,
    ssim_message.msgfrom AS msgfromid
    FROM ssim_message
    JOIN ssim_pilot ON ssim_message.msgto = ssim_pilot.id
    WHERE ssim_message.msgfrom = :pilot
    GROUP BY ssim_message.msgto
    ORDER BY ssim_message.timestamp DESC");
    $pilot = new pilot(true, true);
    $db->bind(':pilot',$pilot->pilot->id);
    $db->execute();
    return $db->resultSet();
  }

  public function getMessageThread($convo) {
    $db = new database();
    $db->query("SELECT ssim_message.*,
      IF (ssim_message.msgfrom = 0, ssim_message.fromoverride, sender.name)
      AS sender,
      IF (ssim_message.msgfrom = 0, 'NaN', sender.fingerprint)
      AS fingerprint
      FROM ssim_message
      LEFT JOIN ssim_pilot AS sender ON ssim_message.msgfrom = sender.id
      WHERE (ssim_message.msgto = :pilot
      AND ssim_message.msgfrom = :msgfrom) OR
      (ssim_message.msgfrom = :pilot
      AND ssim_message.msgto = :msgfrom)
      ORDER BY ssim_message.timestamp ASC");
    $pilot = new pilot(true, true);
    $db->bind(':pilot',$pilot->pilot->id);
    $db->bind(':msgfrom',$convo);
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
    $pilot = new pilot(true, true);
    $db->bind(':id',$id);
    $db->bind(':pilot',$pilot->pilot->id);
    if ($db->execute()) {
      return "Message deleted";
    } else {
      return "Unable to delete message";
    }
  }

    public function deleteMessageThread($fromid) {
    $db = new database();
    $db->query("DELETE FROM ssim_message
      WHERE ssim_message.msgto = :pilot
      AND ssim_message.msgfrom = :fromid");
    $pilot = new pilot(true, true);
    $db->bind(':fromid',$fromid);
    $db->bind(':pilot',$pilot->pilot->id);
    if ($db->execute()) {
      return "Thread deleted";
    } else {
      return "Unable to delete thread";
    }
  }

  public function getUnreadCount() {
    $db = new database();
    $db->query("SELECT count(*) AS count FROM ssim_message
      WHERE ssim_message.msgto = :pilot
      AND ssim_message.read = 0");
    $pilot = new pilot(true, true);
    $db->bind(':pilot',$pilot->pilot->id);
    $db->execute();
    return $db->single()->count;
  }

}

// CREATE TABLE `ssim_message` (
//   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
//   `msgto` int(11) DEFAULT NULL,
//   `msgfrom` int(11) DEFAULT '0',
//   `messagebody` longtext,
//   `sendnode` varchar(128) DEFAULT NULL,
//   `recvnode` varchar(128) DEFAULT NULL,
//   `fromoverride` varchar(64) DEFAULT NULL,
//   `timestamp` timestamp NULL DEFAULT NULL,
//   `read` tinyint(1) NOT NULL DEFAULT '0',
//   PRIMARY KEY (`id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
