<?php

    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/errorPage.php');

    class Redirect {

        public static function RedirectURL($url, $status_code = 302, $replace = true) {
            if (headers_sent() === false)
            {
                header('Location: ' . $url, $replace, $status_code);
            }
            exit();
        }
        
        public static function RequireUser($user) {            
            if ($user === "") {
                ErrorPage::Render(403);
                exit();
            }
        }
        
        public static function RequireNoUser($user) {  
            if ($user !== "") {
                Redirect::RedirectURL("/");
                exit();
            }
        }

    }

?>
