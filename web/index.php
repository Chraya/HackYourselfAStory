<!DOCTYPE html>
<html>
  <head>
    <title>Hack Yourself a Story</title>
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
