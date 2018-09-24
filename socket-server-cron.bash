#!/bin/bash
SOCKETSERVERRESULT=$(/usr/local/bin/php /usr/local/www/patrik-ws.box.ski/socket-server-checker.php)

if [ "$SOCKETSERVERRESULT" == "false" ]
  then
    nohup /usr/local/bin/php /usr/local/www/patrik-ws.box.ski/socket.php &>/dev/null &
fi

if [ "$SOCKETSERVERRESULT" == "debug" ]
  then
    nohup pkill -f SOCKETSERVER &>/dev/null &
fi