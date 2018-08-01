<?php
	//创建websocket服务器对象，监听127.0.0.1:10086端口
	$ws = new swoole_websocket_server("0.0.0.0", 10086);




	//连接本地的 Redis 服务
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $ws->redis = $redis;
    
    //设置 redis 字符串数据
    $redis->set("tutorial-name", "Redis tutorial");
    // 获取存储的数据并输出
    echo "Stored string in redis:: " . $redis->get("tutorial-name");


	$ws->nickNames = array(
		"开心的回转寿司",
		"轻松的大西瓜",
		"有点伤感的茄子",
		"并不难受的火腿肠",
		"略带心事的沙田柚",
		"气愤难平的肉卷",
		"想要洗澡的感冒灵颗粒",

	);


	//监听WebSocket连接打开事件
	$ws->on('open', function ($ws, $request) {
	    print_r($request->server);
	    $key = array_rand($ws->nickNames,1);
	    $nick = $ws->nickNames[$key];
	    unset($ws->nickNames[$key]);
	    $ws->redis->set($request->fd, $nick);

	    $ws->push($request->fd, "你获取的昵称是：{$nick}\n");
	});

	//监听WebSocket消息事件
	$ws->on('message', function ($ws, $frame) {
	    $nick = $ws->redis->get($frame->fd);
	    echo "昵称是:{$nick}\n";
	    foreach($ws->connections as $fd){
	    	$ws->push($fd, "{$nick}:{$frame->data}");
	    }
	});

	//监听WebSocket连接关闭事件
	$ws->on('close', function ($ws, $fd) {
	    echo "client-{$fd} is closed\n";
	});

	$ws->start();




?>