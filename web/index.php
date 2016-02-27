<?php
  require (dirname(__FILE__) . "/inc/config.inc.php");
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Hack Yourself a Story</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.6/slate/bootstrap.min.css" rel="stylesheet" integrity="sha384-X9JiR5BtXUXiV6R3XuMyVGefFyy+18PHpBwaMfteb/vd2RrK6Gt4KPenkQyWLxCC" crossorigin="anonymous">
    <link href="css/styles.css" rel="stylesheet">
    <script src="https://js.pusher.com/3.0/pusher.min.js"></script>
    <script src="http://code.jquery.com/jquery-2.2.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <script src="https://cdn.rawgit.com/leggetter/pusher-js-client-auth/master/dist/pusher-js-client-auth.js"></script>
    <script src="js/bootstrap-growl/jquery.bootstrap-growl.min.js"></script>
    <script>
      var pusher = null;
      var name = null;

      function login()
      {
        name = $('#nickname').val();
        pusher = new Pusher('<?=PUSHER_APP_KEY; ?>',
          {
            authTransport: 'client',
            clientAuth:
            {
              key: '<?=PUSHER_APP_KEY; ?>',
              secret: '<?=PUSHER_APP_SECRET; ?>',
              user_id: name,
              user_info: {}
            }
          }
        );
        $('#loginModal').modal('hide');

        $.bootstrapGrowl("Welcome, " + name + "!",
        {
          ele: 'body',
          type: 'success',
          offset: { from: 'top', amount: 60 },
          align: 'right',
          width: 250,
          delay: 3000,
          allow_dismiss: true,
          stackup_spacing: 10
        });
      }


      $(document).ready(function()
      {
        $('#loginModal').modal('show');
        $('#play').click(function()
        {
          login();
        });
      });

      Pusher.log = function(message)
      {
        if (window.console && window.console.log)
        {
          window.console.log(message);
        }
      };


      // var pusher = new Pusher('7d0b4730386735df8793',
      // {
      //   encrypted: true
      // });

      // var channel = pusher.subscribe('threewords');
      // channel.bind('')
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
              <a class="nav-link" href="profile.html">About Us</a>
            </li>
        </ul>
      </div>
      <!-- <div class="container">
        <br>
        <h1 align="center">Choose The Words!</h1>
        <br>
      </div> -->
    </nav>
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Choose a Nickname</h4>
          </div>
          <div class="modal-body">
            <div class="container">
              <h2>Please choose a nickname</h2>
              <div class="col-md-12">
                <form class="form-inline">
                  <div class="form-group">
                    <input type="text" id="nickname" maxlength="64" class="form-control" placeholder="Enter a Nickname" />
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" id="play" class="btn btn-primary">Let's play!</button>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
