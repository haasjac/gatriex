<?php

	//require_once($_SERVER['DOCUMENT_ROOT'] . '/library/response.php');

	class Wamp {
        
		public static function SendMessage($topic, $message) {
			$context = new ZMQContext();
			$socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'GtxWampServer');
			$socket->connect("tcp://localhost:5555");

			$wampMessage = [
				"topic"		=> $topic,
				"message"	=> $message
			];

			$socket->send(json_encode($wampMessage));
		}

		public static function EncodeGuid($guid) {
			return str_replace("-", ".", $guid);
		}

	}

	class WampMessage {
		public $category = "";        
        public $data = array();
	}

?>