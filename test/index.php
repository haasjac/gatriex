<?php

	require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = Twig::GetTwig()->load('test/test.twig');
    $twigOptions = Twig::GetTwigOptions();

    echo $template->render($twigOptions);

	//echo phpinfo();

	/*require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');

	echo "hello world!";

	// This is our new stuff
    $context = new ZMQContext();
    $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
    $socket->connect("tcp://localhost:5555");

	$response = new Response();
    $response->data["test"] = "Hello World!";
    $response->valid = true;

    $socket->send(json_encode($response));*/

?>
