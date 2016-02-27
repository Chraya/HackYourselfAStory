<?php
  require(dirname(__FILE__) . "/inc/config.inc.php");
  require( dirname(__FILE__) . "/inc/pusher/lib/Pusher.php");

  $mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

  $pusher = new Pusher(
    PUSHER_APP_KEY,
    PUSHER_APP_SECRET,
    PUSHER_APP_ID,
    array('encrypted' => true)
  );

  function SendToClients($event, $jsonData)
  {
    global $pusher;
    $pusher->trigger('threewords', $event, $jsonData);
  }

  function GetSentence()
  {
    global $mysqli;
    // $range_result = $mysqli->query("SELECT MAX(`id`) AS max_id,
    //   MIN(`id`) AS min_id FROM starts");

    $result = $mysqli->query("SELECT starts.* FROM (SELECT FLOOR (RAND() *
      (SELECTcount(*) FROM starts)) num ,@num:=@num+1 from (SELECT @num:=0)
      a , starts LIMIT 1) b ,  starts WHERE b.num=starts.id;")

    // $range_row = $mysqli->fetch_object($range_result);
    // $random = mt_rand($range_row->min_id, $range_row->max_id);

    // $result = $mysqli->query("SELECT * FROM starts WHERE
    //   id >= $random LIMIT 0,1");

    $data = $result->fetch_array(MYSQLI_ASSOC);

    return $data['text'];
  }


  while(1)
  {
    global $mysqli;
    $sentence = GetSentence();
    while(str_word_count($sentence) < 30)
    {
      SendToClients('new_phrase', "{'phrase': '" . $sentence . "'}");
      sleep(10);
      $request = $mysqli->query("SELECT * FROM suggestions");
      $suggestions = array("suggessions" => array());
      while ($row = $request->fetch_array(MYSQLI_ASSOC))
      {
        $suggestions['suggestions'][] = $row['threewords'];
      }
      SendToClients('vote_request', json_encode($suggestions));
      sleep(10);

    }


  }


?>
