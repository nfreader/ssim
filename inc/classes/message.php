<?php

class message {

  //Messages are simple one-to-one threads without subjects or anything else.
  //Just because anything more than that is silly and a huge pain in the butt
  //If a message sender is 0, that means this is a system message and that it
  //needs to have a specific sender set.

  //Node IDs are hexprint(systname.systid). Tricky part is getting those
  //values without grabbing a bigass $pilot->pilot object. Ugh.

  public function newPilotMessage($to,$content) {
    $sender = new pilot(true, true);
    $receiver = new pilot(true, true, $to);
    $db = new database();
    $db->query("INSERT INTO ssim_message
      (msgto, msgfrom, messagebody, sendnode, recvnode, timestamp)
      VALUES (:msgto, :msgfrom, :messagebody, :sendnode, :recvnode, NOW())");
    $db->bind(':msgto', $to);
    $db->bind(':msgfrom', $sender->pilot->id);
    $db->bind(':messagebody', $content);
    $db->bind(':sendnode', $this->getNodeID($sender->pilot->id));
    $db->bind(':recvnode', $this->getNodeID($receiver->pilot->id));
    $db->execute();
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

  public function getPilotThreads($to) {
    $db = new database();
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
