<?php

require 'vendor/autoload.php';

function post_return($result = false, $title="", $message=""){
  $phpObj = (object) ["result" => $result, "title" => $title, "message" => $message];
  return json_encode($phpObj, JSON_FORCE_OBJECT);
}

$chatID = trim(urldecode($_POST['chatID']));
$userID = trim(urldecode($_POST['userID']));
$message = trim(urldecode($_POST['message']));
$event = trim(urldecode($_POST['event']));

$triggerArray = array(
  'chatID' => $chatID,
  'userID' => $userID,
  'message' => $message,
  'event' => $event
);

\Ratchet\Client\connect('ws://patrik-ws.box.ski:8080/chat')->then(function($conn) use($triggerArray) {

    $conn->send(json_encode($triggerArray));
    $conn->close();

    echo post_return(true);

}, function ($e) {
    echo "Could not connect: {$e->getMessage()}\n";
});

