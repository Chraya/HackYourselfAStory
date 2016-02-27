<?php
  require(dirname(__FILE__) . "/inc/config.inc.php");
  require( dirname(__FILE__) . "/inc/pusher/lib/Pusher.php");

  global $mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

  global $pusher = new Pusher(
    PUSHER_APP_KEY,
    PUSHER_APP_SECRET,
    PUSHER_APP_ID,
    array('encrypted' => true)
  );

  function SendToClients($event, $jsonData)
  {
    $pusher->trigger('threewords', $event, $jsonData);
  }

  function GetSentence()
  {
    $range_result = $mysqli->query("SELECT MAX(`id`) AS max_id,
      MIN(`id`) AS min_id FROM starts");

    $range_row = $mysqli->fetch_object($range_result);
    $random = mt_rand($range_row->min_id, $range_row->max_id);

    $result = $mysqli->query("SELECT * FROM starts WHERE
      id >= $random LIMIT 0,1");

    $data = $result->fetch_array(MYSQLI_ASSOC);

    return $data['text'];
  }


  while(1)
  {
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
