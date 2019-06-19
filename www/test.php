<!--
<form action="work.php">
    <input type="text" name="bath" required>
</form>
-->

<?php

$config = array(
    'ssl_key' => './websocket/ssl-cert/privkey.pem',
    'ssl_cert' => './websocket/ssl-cert/cert.pem'
);

$ws = new swoole_websocket_server('warzone.kro.kr',8080,SWOOLE_BASE, SWOOLE_SOCK_TCP | SWOOLE_SSL);

$ws->set($config);

$ws->on('open',function($ws, $req){
    var_dump($req->fd, $req->get, $req->server);
    $ws->push($req->fd,"Hello, welcome\n");
});

$ws->on('message',function($ws, $frame){
    echo "Message : ".$frame->data."\n";
    $ws->push($frame-fd, $frame->data);
});

$ws->on('close',function($ws, $fd){
    echo "client-$fd is closed\n";
});

$ws->start();

?>
