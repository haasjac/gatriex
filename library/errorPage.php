<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');

    class myErrorPage {
        function render($error_code) {
            global $twig;

            $error_path = $_SERVER['DOCUMENT_ROOT'] . "/pages/error/" . $error_code . ".twig";

            if (file_exists($error_path)) {
                $error_path = "error/" . $error_code . ".twig";
            } else {
                $error_path = "error/generic.twig";
            }

            $template = $twig->load($error_path);
            $twig_options = getTwigOptions();

            $twig_options["ErrorCode"] = $error_code;
            
            http_response_code($error_code);
            echo $template->render($twig_options);
        }
    }
    
    $errorPage = new myErrorPage();
?>
