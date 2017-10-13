<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = $twig->load('test/test.twig');
    $twig_options = getTwigOptions();
        
    //echo $template->render($twig_options);
    
    $r = array("data" => "test", "other" => "stuff");
    
    $t = var_export($x, true);
    
    echo $t;
?>