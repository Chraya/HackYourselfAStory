<?php
  //
  // Database config
  //
  require("../conf/dbpasswd.php"); // Include the database password from another file
  define("DB_HOST", "localhost");
  define("DB_USERNAME", "HackYourselfAStory");
  define("DB_DATABASE", "HackYourselfAStory");

  //
  // Pusher config
  //
  require("../conf/pushersecret.php"); // Include the Pusher secret from another file
  define("PUSHER_APP_ID", "183533");
  define("PUSHER_APP_KEY", "7d0b4730386735df8793");
?>
