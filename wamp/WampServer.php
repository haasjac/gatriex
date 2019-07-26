<?php

	require dirname(__DIR__) . '/vendor/autoload.php';

	// This code came from https://github.com/voryx/Thruway/issues/96#issuecomment-98416776

	$loop   = \React\EventLoop\Factory::create();
	$pusher = new \Thruway\Peer\Client("realm1", $loop);

	$pusher->on('open', function ($session) use ($loop) {
		$context = new React\ZMQ\Context($loop);
		$pull    = $context->getSocket(ZMQ::SOCKET_PULL);
		$pull->bind('tcp://127.0.0.1:5555');

		$pull->on('message', function ($entry) use ($session) {
			$entryData = json_decode($entry, true);
			if (isset($entryData['topic']) && isset($entryData['message'])) {
				$session->publish($entryData['topic'], [$entryData['message']]);
			}
		});
	});

	$router = new Thruway\Peer\Router($loop);
	$router->addInternalClient($pusher);
	$router->addTransportProvider(new Thruway\Transport\RatchetTransportProvider("0.0.0.0", 8888));
	$router->start();

?>