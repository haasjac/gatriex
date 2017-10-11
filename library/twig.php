<?php
    declare(strict_types=1);
    require_once (filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/vendor/autoload.php');
    require_once (filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/library/authentication.php');
    
    $loader = new Twig_Loader_Filesystem(filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/pages');
    $twig = new Twig_Environment($loader, array(
        //'cache' => 'compilation_cache',
    ));
    
    function getTwigOptions(): Array {
        $twig_options = array();
        $user = getCurrentUser();
        
        $twig_options["Username"] = $user;
        
        return $twig_options;
    }
?>