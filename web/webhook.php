<?php
  require(dirname(__FILE__) . "/inc/config.inc.php");

  function var_dump_err($someVar)
  {
    ob_start();
    var_dump($someVar);
    $result = ob_get_clean();
    return $result;
  }

  $app_key = $_SERVER['HTTP_X_PUSHER_KEY'];
  $webhook_signature = $_SERVER['HTTP_X_PUSHER_SIGNATURE'];

  $body = file_get_contents('php://input');

  error_log("php://input shows:" . $body . "\n");

  $expected_signature = hash_hmac('sha256', $body, PUSHER_APP_SECRET, false);

  if ($webhook_signature == $expected_signature)
  {
    error_log("Signature check succeeded\n");

    $payload = json_decode($body, true);
    error_log("JSON Error: " . json_last_error_msg() . "\n");
    error_log(var_dump_err($payload));

    $mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

    foreach($payload['events'] as &$event)
    {
      $data = json_decode($event['data'], true);
      error_log("Received: " . var_dump_err($event) . "\n");
      if ($event['event'] == "client-submit_phrase")
      {
        error_log("Client submission - " . $data['phrase'] . "\n");

        $result = $mysqli->query("SELECT * FROM suggestions WHERE
          threewords = '" . $mysqli->real_escape_string($data['phrase'])
          . "'");

        if ($result->num_rows > 0)
        {
          $row = $result->fetch_array(MYSQLI_ASSOC);
          $mysqli->query("UPDATE suggestions SET `count` = `count` + 1" .
          " WHERE `id` = " . $row['id']);
        }
        else
        {
          $query = "INSERT INTO suggestions VALUES(NULL, " .
            $mysqli->real_escape_string($data['phrase']) . ", 1)";
            
          var_dump("About to run " . $query . "\n");
          $mysqli->query($query);
        }
      }
      else if ($event['event'] == "client-submit_vote")
      {
        $mysqli->query("UPDATE suggestions SET `count` = `count` + 1" .
        " WHERE `id` = " . $event['data']['phraseid']);
      }
    }

    header("Status: 200 OK");
  }
  else
  {
    header("Status: 401 Not authenticated");
  }
?>
