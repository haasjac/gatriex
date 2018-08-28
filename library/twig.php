<?php
    declare(strict_types=1);
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/authentication.php');
    
    $loader = new Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'] . '/pages');
    $twig = new Twig_Environment($loader, array(
        //'cache' => 'compilation_cache',
    ));
    
    function getTwigOptions(): Array {        
        $twig_options = array();
        $user = Authentication::GetCurrentUser();
        
        $twig_options["Username"] = $user;
        $twig_options["year"] = date("Y");
        
        return $twig_options;
    }
?>