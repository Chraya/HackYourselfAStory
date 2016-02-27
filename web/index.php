<!DOCTYPE html>
<html>
  <head>
    <title>Hack Yourself a Story</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.6/slate/bootstrap.min.css" rel="stylesheet" integrity="sha384-X9JiR5BtXUXiV6R3XuMyVGefFyy+18PHpBwaMfteb/vd2RrK6Gt4KPenkQyWLxCC" crossorigin="anonymous">
    <script src="https://js.pusher.com/3.0/pusher.min.js"></script>
    <script>
      Pusher.log = function(message)
      {
        if (window.console && window.console.log)
        {
          window.console.log(message);
        }
      };

      var pusher = new Pusher('7d0b4730386735df8793',
      {
        encrypted: true
      });

      var channel = pusher.subscribe('threewords');
      channel.bind('')
    </script>
  </head>
  <body>

  </body>
</html>
