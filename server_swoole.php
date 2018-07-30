<?php
	//创建websocket服务器对象，监听127.0.0.1:10086端口
	$ws = new swoole_websocket_server("127.0.0.1", 10086);

	$fds = new swoole_table(1024);
	$fds->column('fd', swoole_table::TYPE_INT);
	$fds->create();
	$ws->fds = $fds;

	//监听WebSocket连接打开事件
	$ws->on('open', function ($ws, $request) {
	    print_r($request->server);
	    $ws->fds->set($request->fd,array("gid"=>$request->fd));
	    $ws->push($request->fd, "this is swoole websocket server.\n");
	});

	//监听WebSocket消息事件
	$ws->on('message', function ($ws, $frame) {
	    echo "Message: {$frame->data}\n";
	    foreach($ws->fds as $fd=>$v){
	    	$ws->push($fd, "server:不管你说什么，我只说你好。");
	    }
	});

	//监听WebSocket连接关闭事件
	$ws->on('close', function ($ws, $fd) {
	    echo "client-{$fd} is closed\n";
	});

	$ws->start();

?>