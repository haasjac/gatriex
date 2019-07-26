<?php

	require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/wamp.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    /*$template = Twig::GetTwig()->load('test/test.twig');
    $twigOptions = Twig::GetTwigOptions();

    echo $template->render($twigOptions);*/

	//echo phpinfo();

	echo "hello world!";

	$m = array(
		"a" => "b",
		"c" => "d"
	);

	Wamp::SendMessage("test", $m);

	