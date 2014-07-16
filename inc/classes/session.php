<?php

//define('TBL_PREFIX','ssim_');

//Hey! Hey you! You should change every instance of 
//  ssim_
//to your preferred table prefix! It's not that big a deal really, but it'll
//make your code a lot cleaner!

class session implements \SessionHandlerInterface {

    public function __construct() {
      session_set_save_handler(
            array(&$this, 'open'),
            array(&$this, 'close'),
            array(&$this, 'read'),
            array(&$this, 'write'),
            array(&$this, 'destroy'),
            array(&$this, 'gc')
        );
      session_start();
      register_shutdown_function('session_write_close');
    }

    public function open($savePath, $session_name) {
      $db = new database();
      $db->query("INSERT INTO ssim_session
                  SET session_id = :sessionName,
                  session_data = ''
                  ON DUPLICATE KEY 
                  UPDATE session_lastaccesstime = NOW()");
      $db->bind(':sessionName',$session_name);
      $db->execute();
      return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
      $db = new database();
      $db->query("SELECT * FROM ssim_session WHERE session_id = :id");
      $db->bind(':id',$id);
      if ($db->execute()) {
        $result = $db->single(\PDO::FETCH_ASSOC);
        return $result["session_data"];
      }
      return '';
    }

    public function write($id, $data) {
      if ($data == null) {
        return true;
      }
      $db = new database();
      $db->query("INSERT INTO ssim_session 
                  SET session_id = :id, 
                  session_data = :data
                  ON DUPLICATE KEY UPDATE session_data = :data");
      $db->bind(':id',$id);
      $db->bind(':data',$data);
      $db->execute();
      //session_write_close();
    }

    public function destroy($id) {
      $db = new database();
      $db->query("DELETE FROM ssim_session WHERE session_id = :id");
      $db->bind(':id',$id);
      $db->execute();
      return true;
    }

    public function gc($maxlifetime) {
      $db = new database();
      $db->query("DELETE FROM ssim_session 
                  WHERE session_lastaccesstime < DATE_SUB(NOW(),
                  INTERVAL " . $lifetime . " SECOND)");
      $db->execute();
      return true;
    }
}