<?php
  require (dirname(__FILE__) . "/inc/config.inc.php");
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Hack Yourself a Story</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.6/slate/bootstrap.min.css" rel="stylesheet" integrity="sha384-X9JiR5BtXUXiV6R3XuMyVGefFyy+18PHpBwaMfteb/vd2RrK6Gt4KPenkQyWLxCC" crossorigin="anonymous">
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/font-awesome-4.5.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://js.pusher.com/3.0/pusher.min.js"></script>
    <script src="http://code.jquery.com/jquery-2.2.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <script src="https://cdn.rawgit.com/leggetter/pusher-js-client-auth/master/dist/pusher-js-client-auth.js"></script>
    <script src="js/bootstrap-growl/jquery.bootstrap-growl.min.js"></script>
    <script>
      var pusher = null;
      var name = null;
      var channel = null;
      var presenceChannel = null;
      var pusherEndpoint = 'https://api.pusherapp.com/apps/<?=PUSHER_APP_ID; ?>/events';

      var voted = false;

      function decrementTimer()
      {
        if ($('#countdownTimer').html() != '0')
          $('#countdownTimer').html(parseInt($('#countdownTimer').html(),
            10) - 1);
      }

      function customGrowl(text)
      {
        $.bootstrapGrowl(text,
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
        $('body').on('click', '.voteLink', function(event)
        {
          event.preventDefault();
          var id = event.target.id;
          console.log("Voting for " + id);
          if(!voted)
          {
            submitVoteToServer(id);
            voted = true;
          }
        });

        $('body').on('click', '#phraseInputSubmit', function(event)
        {
          event.preventDefault();
          var entry = $('#phraseInputBox').val();
          console.log("User entered: " + entry);
          submitPhraseToServer(entry);
          $('#phraseInputSubmit').attr('disabled', true);
        });
      });

      function newPhrase(data)
      {
        // When the server sends out a new phrase
        var obj = JSON.parse(data);
        $('#StoryPlaceholder').html(obj['phrase']);
        $('#voteLinkDiv').html('                                              \
        <form id="phraseForm" class="form-inline">                            \
          <div class="form-group">                                            \
            <input type="text" class="form-control" id="phraseInputBox" />    \
            <button type="button" class="form-control" id="phraseInputSubmit">\
              Suggest &raquo;                                                 \
            </button>                                                         \
          </div>                                                              \
        </form>                                                               \
        ');
        voted = false;
      }

      function voteResult(data)
      {
        // When the server delivers the vote result
        var obj = JSON.parse(data);
        $('#voteLinkDiv').html("<h3>The winning phrase was: " + obj['winningtext'] + "</h3>");
      }

      function voteRequest(data)
      {
        // When the server asks the users to vote (thus delivering all vote options)
        var obj = JSON.parse(data);
        console.dir(obj);
        var suggestions = obj['suggestions'];
        var html = "// ";
        for (var key in suggestions)
        {
          html = html + '<a href="#" id="' + key + '" class="voteLink">'
            + suggestions[key] + '</a> // ';
        }
        $('#voteLinkDiv').html(html);
      }

      function submitPhraseToServer(phrase)
      {
        console.log("Sending this phrase: " + phrase);
        var triggered = channel.trigger('client-submit_phrase',
          { 'phrase' : phrase });
      }

      function submitVoteToServer(phraseId)
      {
        var triggered = channel.trigger('client-submit_vote',
          { 'phraseid' : phraseId });
      }

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

        channel = pusher.subscribe('<?=PUSHER_CHANNEL; ?>');
        channel.bind('new_phrase', function(data)
        {
          $('#countdownTimer').html('10');
          newPhrase(data);
          console.log(data);
        });
        channel.bind('vote_result', function(data)
        {
          $('#countdownTimer').html('5');
          voteResult(data);
          console.log(data);
        });
        channel.bind('vote_request', function(data)
        {
          $('#countdownTimer').html('10');
          voteRequest(data);
          console.log(data);
        });
        
        customGrowl("Welcome, " + name + "!");

        presenceChannel = pusher.subscribe('presence-threewords');
        presenceChannel.bind('pusher:subscription_succeeded', function(members)
        {
          $('#onlineCount').html("0");
          members.each(function(member)
          {
            $('#onlineCount').html(parseInt($('#onlineCount').html(), 10) + 1);
            console.log(member['user_id']);
          });
        });

        presenceChannel.bind('pusher:member_added', function(member)
        {
          customGrowl(member['user_id'] + " has come online");
          console.log(member['user_id'] + " came online");
          $('#onlineCount').html(parseInt($('#onlineCount').html(), 10) + 1);
        });

        presenceChannel.bind('pusher:member_removed', function(member)
        {
          customGrowl(member['user_id'] + " has left");
          console.log(member['user_id'] + " left");
          $('#onlineCount').html(parseInt($('#onlineCount').html(), 10) - 1);
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

    </script>
  </head>
  <body>
    <nav class="navbar navbar-dark navbar-fixed-top navbar-transparent navbar-inner">
      <div class="container">
        <ul class="nav navbar-nav">
          <li class="active"><a class="navbar-brand" href="/">Hack Yourself a Story!</a></li>
          <li>Time Remaining:</li>
          <li><span id="countdownTimer">0</span></li>
        </ul>
      </div>
    </nav>
    <div class="container">
      <div class="jumbotron">
        <h2>Current Story</h2>
        <h3 id="StoryPlaceholder"></h3>
      </div>
    </div>
    <div class= "container">
      <div class="jumbotron">
          <div class="container">
            <h3>
              Suggest your own three word phrase below, then vote for
              the funniest by clicking
            </h3>
            <div id="voteLinkDiv"></div>
          </div>
      </div>
    </div>
    <div class="container">
      <div class="jumbotron">
        <h4><span id="onlineCount">0</span> people online.</h4>
      </div>
    </div>
    <div class="container align-middle align-bottom">
      <i class="fa fa-facebook"></i>
      <i class="fa fa-github"></i>
    </div>
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
