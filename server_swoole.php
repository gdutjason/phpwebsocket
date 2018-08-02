<?php
	//创建websocket服务器对象，监听127.0.0.1:10086端口
	$ws = new swoole_websocket_server("0.0.0.0", 10086);




	//连接本地的 Redis 服务
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $ws->redis = $redis;
    
    //设置 redis 字符串数据
    //$redis->set("tutorial-name", "Redis tutorial");
    // 获取存储的数据并输出
    //echo "Stored string in redis:: " . $redis->get("tutorial-name");


	$nickNames = array(
		"开心的回转寿司",
		"轻松的大西瓜",
		"有点伤感的茄子",
		"并不难受的火腿肠",
		"略带心事的沙田柚",
		"气愤难平的肉卷",
		"想要洗澡的感冒灵颗粒",
		"贪睡的栀子花",
		"林枫",
		"克行",
		"妒天",
		"土豆味的马铃薯",
		"番茄味的西红柿",
		"肚子饿了的汉堡包",
	);

	$redis->set("nickNames",json_encode($nickNames));


	//监听WebSocket连接打开事件
	$ws->on('open', function ($ws, $request) {
	    //print_r($request->server);
		$jsonData = $ws->redis->get("nickNames");
		$nickNames = json_decode($jsonData,true);
		print_r($nickNames);


	    $key = array_rand($nickNames,1);
	    $nick = $nickNames[$key];
	    unset($nickNames[$key]);
	    //print_r($nickNames);
	    $ws->redis->set("nickNames",json_encode($nickNames));
	    $ws->redis->set($request->fd, $nick);
	    //给本人的提示
	    $ws->push($request->fd, "你获取的昵称是：{$nick}\n当前本聊天室人数为:".count($ws->connections)."\n");
	    //给其他人的提示
	    sendToAll("{$nick}加入了聊天室，当前本聊天室人数为:".count($ws->connections)."\n",$request->fd);

	});

	//监听WebSocket消息事件
	$ws->on('message', function ($ws, $frame) {
	    $nick = $ws->redis->get($frame->fd);
	    sendToAll("{$nick}:{$frame->data}");
	});

	//向全体发送消息,后面的fd为过滤的本人通道
	function sendToAll($msg,$filterFd=0){
		global $ws;
		foreach($ws->connections as $fd){
			if($fd == $filterFd){

			}else{
	    		$ws->push($fd, "{$msg}");
			}
	    }
	}

	//监听WebSocket连接关闭事件
	$ws->on('close', function ($ws, $fd) {
		$nick = $ws->redis->get($fd);
	    sendToAll("{$nick}退出了了聊天室，当前本聊天室人数为:".count($ws->connections)."\n");
	    echo "client-{$fd} is closed\n";
	});

	$ws->start();




?>