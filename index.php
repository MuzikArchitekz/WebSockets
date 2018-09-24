<?php

  // always one if not set
  $chatID = 1;

  // if someone sets a custom chat ID
  if(isset($_GET['chatID']) && is_numeric($_GET['chatID'])){
    $chatID = trim($_GET['chatID']);
  }

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
  <title>Chatty | Chat Room # <?php echo $chatID; ?></title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
    crossorigin="anonymous">


  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <style>
    #chatRoom {
      height: 400px;
      overflow-y: scroll;
    }
  </style>


</head>

<body>

  <div class="container">

    <div class="row justify-content-center">

      <div class="col-12 col-sm-10 col-md-8">
        <h1>Chat Room: #<?php echo $chatID; ?></h1>
        <div class="form-group">
          <div class="form-control" id="chatRoom"></div>
        </div>
        <hr>
        <form id="chatRoomMessageForm">

          <input type="hidden" id="chatID" value="<?php echo $chatID; ?>" />

          <div class="form-group">
            <label for="message">Message</label>
            <textarea class="form-control" id="message"></textarea>
          </div>

          <button class="btn btn-primary btn-large btn-block" type="submit">Send</button>

        </form>
      </div>

    </div>




  </div>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
    crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
    crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
    crossorigin="anonymous"></script>

  <script>

    // js socket global
    var chatSocket;

    function appendMessageToBox(userID, message) {
      var messageHTML = `<div class="col-12"><h4><strong>${userID}: </strong> ${message}</h4></div>`;
      $('#chatRoom').append(messageHTML);
    }

    $(document).ready(function () {


      // open a web socket
      chatSocket = new WebSocket("wss://patrik-ws.box.ski/wss2/chat");

      // send initial message to server when web socket is opened
      chatSocket.onopen = function (event) {
        var initialWSObj = {
          chatID: parseInt($('#chatID').val()),
          event: 'user_joined',
        };
        chatSocket.send(JSON.stringify(initialWSObj));
      };

      // when a message is recieved
      chatSocket.onmessage = function (e) {
        console.log(e);

        var data = JSON.parse(e.data);

        switch(data.event){
          case 'message_sent':
            appendMessageToBox(data.userID, data.message);
          break;
          case 'user_joined':
            appendMessageToBox(data.userID, 'Welcome Our User!');
          break;
          case 'message_history_sent':
            for(var i = 0; i< data.history.length; i++){
              appendMessageToBox(data.history[i].userID, data.history[i].message);
            }

          break;
        }

        // scroll to bottom
        var chatRoom = document.getElementById('chatRoom');
        chatRoom.scrollTop = chatRoom.scrollHeight;

      }

      // allow enter to submit form while in textarea but allow SHIFT + ENTER to make a new line
      $("#message").keypress(function (e) {
          if(e.which == 13 && !e.shiftKey) {
              $(this).closest("form").submit();
              e.preventDefault();
              return false;
          }
      });

      // submit form
      $('#chatRoomMessageForm').on('submit', function (e) {
        e.preventDefault();

        if($('#message').val().trim() !== ''){
          var message = $('#message').val();
          var sendMessageWSObj = {
            chatID: parseInt($('#chatID').val()),
            event: 'message_sent',
            message: message,
          };
          chatSocket.send(JSON.stringify(sendMessageWSObj));
          $('#message').val(' ');
        }

      });



    });

  </script>

</body>

</html>