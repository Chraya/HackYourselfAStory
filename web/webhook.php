<?php
  require(dirname(__FILE__) . "/inc/confic.inc.php");

  $app_key = $_SERVER['HTTP_X_PUSHER_KEY'];
  $webhook_signature = $_SERVER['HTTP_X_PUSHER_SIGNATURE'];

  $body = file_get_contents('php://input');

  $expected_signature = hash_hmac('sha256', $body, $app_secret, false);

  if ($webhook_signature == $expected_signature)
  {
    $payload = json_decode($body, true);

    $mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

    foreach($payload['events'] as &$event)
    {
      if ($event['event_name'] == "client-submit_phrase")
      {
        $result = $mysqli->query("SELECT * FROM suggestions WHERE
          threewords = '" . $mysqli->real_escape_string($event['phrase'])
          . "'");

        if ($result->num_rows > 0)
        {
          $row = $result->fetch_array(MYSQLI_ASSOC);
          $mysqli->query("UPDATE suggestions SET `count` = `count` + 1" .
          " WHERE `id` = " . $row['id']);
        }
        else
        {
          $mysqli->query("INSERT INTO suggestions VALUES(NULL, " .
            $mysqli->real_escape_string($event['phrase']) . ", 1)");
        }
      }
      else if ($event['event_name'] == "client-submit_vote")
      {
        $mysqli->query("UPDATE suggestions SET `count` = `count` + 1" .
        " WHERE `id` = " . $event['phraseid']));
      }
    }

    header("Status: 200 OK");
  }
  else
  {
    header("Status: 401 Not authenticated");
  }
?>
