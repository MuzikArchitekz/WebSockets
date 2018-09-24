<?php

require 'vendor/autoload.php';

/**
*
* IF YOU WANT TO KILL THE LIVE PROCESS , COMMENT OUT THIS CODE AND ONLY MAKE IT ECHO "debug" AND SYSTEM WILL AUTOMATICALLY KILL THE PROCESS
*
*/


/*
// this just checks to make sure server is up and running
\Ratchet\Client\connect('ws://patrik-ws.box.ski:8080/chat')->then(function($conn){
    $conn->close();
    echo "true";
}, function ($e) {
    echo "false";
});
*/

echo 'debug';
