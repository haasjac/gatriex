<?php

    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');

    class ErrorPage {

        public static function Render($errorCode) {
            $errorPath = $_SERVER['DOCUMENT_ROOT'] . "/pages/error/" . $errorCode . ".twig";

            if (file_exists($errorPath)) {
                $errorPath = "error/" . $errorCode . ".twig";
            } else {
                $errorPath = "error/generic.twig";
            }

            $template = Twig::GetTwig()->load($errorPath);
            $twigOptions = Twig::GetTwigOptions();

            $twigOptions["ErrorCode"] = $errorCode;
            
            http_response_code($errorCode);
            echo $template->render($twigOptions);
        }

    }
    
?>
