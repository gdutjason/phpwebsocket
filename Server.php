<?php

	$IP = "127.0.0.1";
	$PORT = 10086;

	$socket = createSocket();

	runServer($scoket);

	public function createSocket(){
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		scoket_bind($scoket, $IP, $PORT);
		return $socket;
	}


	public function runServer($socket){
		socket_listen($scoket);

		while (true) {
			if(socket_accept($socket)){
				socket_write($socket, 'hello you are the first to use my server');

				$msg = socket_read($socket, 2048);
				//打印出消息
				echo $msg;
			}
		}
	}



?>