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

  function endsWith($haystack, $needle)
  {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
  }

  function SendToClients($event, $jsonData)
  {
    global $pusher;
    $pusher->trigger(PUSHER_CHANNEL, $event, $jsonData);
  }

  function GetSentence()
  {
    global $mysqli;

    $result = $mysqli->query("SELECT starts.* FROM (SELECT FLOOR (RAND() *
      (SELECT count(*) FROM starts)) num ,@num:=@num+1 from (SELECT @num:=0)
      a , starts LIMIT 1) b ,  starts WHERE b.num=starts.id;")
        or trigger_error($mysqli->error);

    $data = $result->fetch_array(MYSQLI_ASSOC);

    return $data['text'];
  }

  function LockClients()
  {
    $lock = array("action" => "lock");
    SendToClients("lock_unlock", json_encode($lock));
  }

  function UnlockClients()
  {
    $unlock = array("action" => "unlock");
    SendToClients("lock_unlock", json_encode($unlock));
  }

  while(1)
  {
    global $mysqli;
    $sentence = GetSentence() . " ";
    while(str_word_count($sentence) < 30)
    {
      $out = array("phrase" => $sentence);
      SendToClients('new_phrase', json_encode($out));
      UnlockClients();
      sleep(13);
      LockClients();
      sleep(2);
      $request = $mysqli->query("SELECT * FROM suggestions");
      $suggestions = array();
      while ($row = $request->fetch_array(MYSQLI_ASSOC))
      {
        $id = (int)($row['id']);
        $suggestions[$id] = $row['threewords'];
      }

      SendToClients('vote_request', json_encode(array("suggestions" => $suggestions), JSON_NUMERIC_CHECK));
      UnlockClients();
      sleep(8);
      LockClients();
      sleep(2);
      $result = $mysqli->query("SELECT * FROM suggestions ORDER BY count
        DESC LIMIT 1");

      $top = $result->fetch_array(MYSQLI_ASSOC);

      if (!endsWith($sentence, " "))
        $sentence .= " ";

      $sentence .= $top['threewords'];

      SendToClients("vote_result", json_encode(
        array(
          "sentence"    => $sentence,
          "winningtext" => $top['threewords']
            )
          )
        );
      sleep(2);
      // We've had all the suggestions for this round.
      $mysqli->query("TRUNCATE table suggestions");
    }

    // Sentence complete. Store it!
    $mysqli->query("INSERT INTO stories VALUES(NULL, NULL, '" .
      $mysqli->real_escape_string($sentence) . "')");
  }


?>
