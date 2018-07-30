<?php

	$IP = "127.0.0.1";
	$PORT = 10086;

	$socket = new Socket($IP,$PORT);
	$socket->runServer();
	class Socket{
		public $soc;


		public function __construct($ip, $port){
			$this->soc = $this->createSocket($ip, $port);
		}
		public function createSocket($ip, $port){
			$s = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			socket_bind($s, $ip, $port);
			return $s;
		}


		public function runServer(){
			socket_listen($this->soc);

			while (true) {
				if(socket_accept($this->soc)){
					socket_write($this->soc, 'hello you are the first to use my server');

					$msg = socket_read($this->soc, 2048);
					//打印出消息
					echo $msg;
				}
			}
		}

		}

	


?>