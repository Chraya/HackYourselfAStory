<?php
  require("inc/config.inc.php");
  require ("inc/pusher/lib/Pusher.php");

  $mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

  $pusher = new Pusher(
    PUSHER_APP_KEY,
    PUSHER_APP_SECRET,
    PUSHER_APP_ID,
    array('encrypted' => true)
  );

  $data['message'] = 'test';
  $pusher->trigger('threewords', 'test_event', $data);

?>
