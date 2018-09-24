<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class MessageBroker implements MessageComponentInterface {
    public $clients;
    private $connectedUsers;
    private $messageHistory;

    // init socket server storage
    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->messageHistory = array();
    }

    // executed on socket open AKA when a user connects to the socket
    public function onOpen(ConnectionInterface $conn) {
      $this->clients->attach($conn);
      $this->connectedUsers[$conn->resourceId] = array(
        'connection' => $conn,
        'chatID' => null,
        'userID' => null,
      );
      echo "New connection! ({$conn->resourceId})\n";
    }


    // when a message from the open websocket gets sent
    public function onMessage(ConnectionInterface $from, $msg) {
      // turn message into json
      $request = json_decode(trim($msg), true);

      // get chat ID
      $chatID = $request['chatID'];

      // get event
      $event = $request['event'];

      // create a array depending on the event type
      switch(strtolower($event)){
        case 'message_sent':
          $returnArray = array(
            'event' => $event,
            'userID' => $this->connectedUsers[$from->resourceId]['userID'],
            'message' => $request['message'],
          );

          // add to message history ... if chat grows too large, this will crash due to lack of ram until script restarted
          // shoud only keep last hundred in here and have user query previous messages from a database if they scroll up into the chat's history past the 100 messages in this array
          $this->messageHistory[''.$chatID.''][] = $returnArray;
        break;

        case 'user_joined':

          // user id from resource id
          $userID = $from->resourceId;

          $returnArray = array(
            'event' => $event,
            'userID' => $userID,
          );


          // add them as connected users
          $this->connectedUsers[$from->resourceId]['chatID'] = $chatID;
          $this->connectedUsers[$from->resourceId]['userID'] = $userID;

          $historyReturnArray = array(
            'event' => 'message_history_sent',
            'history' => isset($this->messageHistory[''.$chatID.''])  ? $this->messageHistory[''.$chatID.''] : array(),
          );

          // send them a copy of the chat's message history
          $this->connectedUsers[$from->resourceId]['connection']->send(json_encode($historyReturnArray));
        break;
      }

      // broadcast event to entire chatroom
      foreach($this->connectedUsers as $user){
        if($user['chatID'] === $chatID){
          // send array to this user in chat room
          $user['connection']->send(json_encode($returnArray));
        }
      }

    }



    // deletes user from web socket server
    public function onClose(ConnectionInterface $conn) {
      // Detatch everything from everywhere
      $this->clients->detach($conn);
      unset($this->connectedUsers[$conn->resourceId]);
    }

    // handles erros n shit
    public function onError(ConnectionInterface $conn, \Exception $e) {
      $conn->close();
    }

}
