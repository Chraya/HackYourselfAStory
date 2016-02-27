<!DOCTYPE html>
<html>
  <head>
    <title>Hack Yourself a Story</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.6/slate/bootstrap.min.css" rel="stylesheet" integrity="sha384-X9JiR5BtXUXiV6R3XuMyVGefFyy+18PHpBwaMfteb/vd2RrK6Gt4KPenkQyWLxCC" crossorigin="anonymous">
    <link href="css/styles.css">
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
    <nav class="navbar navbar-dark navbar-fixed-top bg-primary">
      <div class="container">
        <a class="navbar-brand">Hack Yourself a Story!</a>
        <ul class="nav navbar-nav pull-right">
          <li class="nav-item active">
            <a class="nav-link" href="index.html">Home</a>
            <li class="nav-item active">
              <a class="nav-link" href="profile.html">Profile</a>
            </li>
          </li>
        </ul>
      </div>
      <div class = "container">
        <br>
        <h1 align="center"> Choose The Words!</h1>
        <br>
      </div>
    </nav>
  </body>
</html>
