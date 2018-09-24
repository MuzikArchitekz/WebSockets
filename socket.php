<?php

cli_set_process_title("SOCKETSERVER");

require 'vendor/autoload.php';
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require 'MessageBroker.php';

$loop = null;
// Run the server application through the WebSocket protocol on port 8080
$app = new Ratchet\App("patrik-ws.box.ski", 8080, '0.0.0.0', $loop);
$app->route('/chat', new MessageBroker, array('*'));

$app->run();